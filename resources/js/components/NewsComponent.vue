<template>
  <div class="news-container">

    <!-- 1) Search input up here -->
    <div class="mb-4">
      <input
        v-model="search"
        type="text"
        class="form-control form-control-lg"
        placeholder="Search articles…"
      />
    </div>

    <div class="row">
      <div
        v-for="article in articles"
        :key="article.id"
        class="col-md-4 mb-4"
      >
        <div class="card h-100">
          <img
            v-if="article.url_to_image"
            :src="article.url_to_image"
            class="card-img-top"
            :alt="article.title"
          />
          <div class="card-body d-flex flex-column">
            <h5 class="card-title">
              {{ truncate(article.title, 60) }}
            </h5>
            <p class="card-text">
              {{ truncate(article.description, 100) }}
            </p>
            <a
              :href="`/news/${article.id}`"
              class="mt-auto btn btn-primary"
            >
              Read More
            </a>
          </div>
        </div>
      </div>
    </div>

    <!-- infinite‐scroll sentinel -->
    <div ref="scrollObserver"></div>

    <!-- loading spinner -->
    <div v-if="loading" class="text-center py-3">
      <span class="spinner-border" role="status"></span>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import debounce from 'lodash/debounce'

export default {
  name: 'NewsComponent',

  data() {
    return {
      articles: [],
      page: 1,
      perPage: 6,
      loading: false,
      finished: false,
      observer: null,

      // 2) track the search term
      search: '',
    }
  },

  watch: {
    // 3) whenever `search` changes, reset and re-fetch
    search: debounce(function (newTerm) {
      this.page     = 1
      this.articles = []
      this.finished = false
      this.fetchNews()
    }, 300)   // debounce so we don’t hammer the server on every keystroke
  },

  methods: {
    async fetchNews() {
      if (this.loading || this.finished) return
      this.loading = true

      try {
        // post to your dedicated load-more route, including search
        const { data } = await axios.post('/news/load-more', {
          page:     this.page,
          per_page: this.perPage,
          search:   this.search.trim() || null,
        })

        // first page replaces, subsequent pages append
        if (data.data.length) {
          if (this.page === 1) this.articles = data.data
          else                this.articles.push(...data.data)
          this.page++
        }

        // stop when we hit last_page
        if (data.current_page >= data.last_page) {
          this.finished = true
          this.disconnectObserver()
        }
      } catch (e) {
        console.error('Failed to load news', e)
      } finally {
        this.loading = false
      }
    },

    truncate(text, length) {
      if (!text) return ''
      return text.length > length
        ? text.slice(0, length) + '…'
        : text
    },

    initObserver() {
      this.observer = new IntersectionObserver(
        entries => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              this.fetchNews()
            }
          })
        },
        { root: null, rootMargin: '0px', threshold: 0.1 }
      )
      this.observer.observe(this.$refs.scrollObserver)
    },

    disconnectObserver() {
      if (this.observer) {
        this.observer.unobserve(this.$refs.scrollObserver)
      }
    }
  },

  mounted() {
    this.fetchNews()
    this.initObserver()
  },

  beforeDestroy() {
    this.disconnectObserver()
  }
}
</script>

<style scoped>
.news-container {
  padding-bottom: 2rem;
}
</style>
