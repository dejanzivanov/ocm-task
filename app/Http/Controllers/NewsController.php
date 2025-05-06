<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\News;

class NewsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function settings(Request $request)
    {
        $setting = \DB::table('settings')->first();

        if (! $setting) {
            return response()->json(['exists' => false]);
        }

        return response()->json([
            'exists' => ! empty($setting->api_key)
        ]);
    }

    public function apiSave(Request $request)
    {
        // 1) Validate presence & basic format
        $request->validate([
            'api_key' => ['required', 'string', 'min:20', 'max:100'],
        ]);


        $apiKey = $request->input('api_key');

        // 2) Test the key with a minimal NewsAPI call
        try {
            $response = Http::withHeaders([
                'X-Api-Key' => $apiKey,
            ])->get('https://newsapi.org/v2/top-headlines', [
                'pageSize' => 1,
                'country'  => 'us',
            ]);
        } catch (\Exception $e) {
            // Network or DNS failure
            return response()->json([
                'error' => 'Unable to reach NewsAPI service. Please try again later.'
            ], 500);
        }

        if ($response->status() === 401) {
            // Unauthorized = bad key
            return response()->json([
                'error' => 'Invalid API key. Please check and try again.'
            ], 401);
        }
        // dd($request->all());


        if ($response->failed()) {
            // Other API error (e.g. rate limit)
            return response()->json([
                'error' => 'NewsAPI error: ' . $response->json('message', 'Unknown error or rate limit has been reached.')
            ], $response->status());
        }

        // 3) No errors → persist the key
        // If you only ever store one setting row, you can truncate first:
        DB::table('settings')->truncate();
        DB::table('settings')->insert([
            'api_key'    => $apiKey,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4) Return success
        return response()->json([
            'message' => 'Your API key has been successfully saved.'
        ]);
    }

    public function apiDelete()
    {
        DB::table('settings')->truncate();
        return response()->json(['message'=>'API key removed']);
    }

    // public function generate(Request $request)
    // {
    //     dd($request->all());   
    // }
    public function generate(Request $request)
    {
        // 1) get stored API key
        $apiKey = DB::table('settings')->value('api_key');
        if (! $apiKey) {
            return response()->json([
                'error' => 'No API key configured.'
            ], 422);
        }

        // 2) build NewsAPI query
        $from = now()->subMonth()->toDateString();
        $params = [
            'q'       => 'tesla',             // topic
            'from'    => $from,               // last month
            'sortBy'  => 'publishedAt',       // newest first
            'pageSize'=> 20,                  // fetch up to 20 articles
            'apiKey'  => $apiKey,
        ];

        // 3) call NewsAPI
        try {
            $response = Http::get('https://newsapi.org/v2/everything', $params);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Unable to reach NewsAPI. Please try again later.'
            ], 500);
        }

        if ($response->status() === 401) {
            return response()->json([
                'error' => 'Invalid API key.'
            ], 401);
        }
        if ($response->failed()) {
            return response()->json([
                'error' => $response->json('message', 'Unknown error')
            ], $response->status());
        }

        // 4) persist each article
        $articles = $response->json('articles', []);
        foreach ($articles as $a) {
            DB::table('news')->insert([
                'source_id'     => $a['source']['id'],
                'source_name'   => $a['source']['name'],
                'author'        => $a['author'],
                'title'         => $a['title'],
                'description'   => $a['description'],
                'url'           => $a['url'],
                'url_to_image'  => $a['urlToImage'],
                'published_at'  => Carbon::parse($a['publishedAt'])->toDateTimeString(),
                'content'       => $a['content'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        // 5) return success
        return response()->json([
            'message' => 'News generated successfully.',
            'count'   => count($articles),
        ]);
    }

    public function index()
    {
        $news = News::orderBy('published_at','desc')
                    ->paginate(6);
        return view('news.index', compact('news'));
    }

    /**
     * Display a single news article.
     */
    public function show($id)
    {
        $article = DB::table('news')->find($id);

        if (! $article) {
            abort(404);
        }

        // dd($article);

        return view('news.show', ['news' => $article]);
    }

    public function loadMore(Request $request)
    {
        $page    = $request->get('page', 1);
    $perPage = $request->get('per_page', 6);
    $search  = $request->input('search');

    $query = News::orderBy('published_at', 'desc');

    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('source_name', 'like', "%{$search}%")
              ->orWhere('author', 'like', "%{$search}%");
        });
    }

    $news = $query->paginate($perPage, ['*'], 'page', $page);

    return response()->json([
        'data'         => $news->items(),
        'current_page' => $news->currentPage(),
        'last_page'    => $news->lastPage(),
    ]);
    }
}
