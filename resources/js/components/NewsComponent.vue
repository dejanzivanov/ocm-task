<template>
  <div class="news-container">
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

    <!-- Sentinel for infinite scroll -->
    <div ref="scrollObserver"></div>

    <!-- Loading spinner -->
    <div v-if="loading" class="text-center py-3">
      <span class="spinner-border" role="status"></span>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

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
    };
  },
  methods: {
    async fetchNews() {
      // avoid duplicate or post-completion calls
      if (this.loading || this.finished) return;
      this.loading = true;

      try {
        const { data } = await axios.post('/news/load-more', {
          page: this.page,
          per_page: this.perPage,
        });

        // append new items
        if (data.data.length) {
          this.articles.push(...data.data);
          this.page++;
        }

        // if we've reached the last page, stop observing
        if (data.current_page >= data.last_page) {
          this.finished = true;
          this.disconnectObserver();
        }
      } catch (err) {
        console.error('Failed to load news', err);
      } finally {
        this.loading = false;
      }
    },

    truncate(text, length) {
      if (!text) return '';
      return text.length > length
        ? text.slice(0, length) + '…'
        : text;
    },

    initObserver() {
      const options = { root: null, rootMargin: '0px', threshold: 0.1 };
      this.observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            this.fetchNews();
          }
        });
      }, options);

      if (this.$refs.scrollObserver) {
        this.observer.observe(this.$refs.scrollObserver);
      }
    },

    disconnectObserver() {
      if (this.observer && this.$refs.scrollObserver) {
        this.observer.unobserve(this.$refs.scrollObserver);
      }
    }
  },

  mounted() {
    this.fetchNews();
    this.initObserver();
  },

  beforeDestroy() {
    this.disconnectObserver();
  }
};
</script>

<style scoped>
.news-container {
  padding-bottom: 2rem;
}
</style>
