<template>
  <div class="container py-4" v-if="article">
    <a href="/news" class="btn btn-link mb-3">
      ← Back to All News
   </a>

    <div class="card mb-4">
      <img
        v-if="article.url_to_image"
        :src="article.url_to_image"
        class="card-img-top"
        :alt="article.title"
      />

      <div class="card-body">
        <h2 class="card-title">{{ article.title }}</h2>

        <p class="text-muted">
          By <strong>{{ article.author || 'Unknown' }}</strong>
          on {{ formattedDate }}
        </p>

        <div class="card-text" v-html="formattedContent"></div>

        <a
          :href="article.url"
          class="btn btn-outline-secondary mt-3"
          target="_blank"
          rel="noopener"
        >
          View Original
        </a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NewsShowComponent',
  props: {
  initialArticle: {
    type: Object,
    required: true
  }
},
  data() {
    return {
      article: null
    }
  },
  computed: {
    formattedDate() {
      if (!this.article.published_at) return ''
      // Ensure it's ISO-compatible: "2025-05-04 23:19:47" → "2025-05-04T23:19:47Z"
      let iso = this.article.published_at
      if (!iso.includes('T')) {
        iso = iso.replace(' ', 'T') + 'Z'
      }
      const dt = new Date(iso)
      return dt.toLocaleString(undefined, {
        year:   'numeric',
        month:  'long',
        day:    'numeric',
        hour:   'numeric',
        minute: '2-digit',
        hour12: true
      })
    },
    formattedContent() {
      // Use the full content if available, otherwise fall back to description
      const raw = this.article.content || this.article.description || ''
      // Escape any HTML and convert newlines to <br>
      return this.escapeHtml(raw).replace(/\r?\n/g, '<br/>')
    }
  },
  methods: {
    escapeHtml(str) {
      return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;')
    }
  },
  mounted() {
    // stash the incoming prop into our local data so it’s reactive
    this.article = this.initialArticle
  }
}
</script>

<style scoped>
.card-text {
  white-space: pre-wrap;
}
</style>
