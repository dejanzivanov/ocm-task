<template>
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
  
          <div class="card">
            <div class="card-header">Control Component</div>
            <div class="card-body">
  
              <!-- Action Buttons -->
              <div class="d-flex justify-content-center gap-2 mb-3">
                <a
                  :class="['btn btn-primary', !exists && 'disabled']"
                  :href="exists ? '/news' : '#'"
                  @click.prevent="exists && visitNews()"
                >Visit News Page</a>
                <button
                    class="btn btn-success"
                    :disabled="!exists || creating"
                    @click="exists && generateNews()"
                    >
                    <span v-if="creating">
                        <span class="spinner-border spinner-border-sm"></span>
                        Create News{{ ellipsis }}
                    </span>
                    <span v-else>
                        Create News
                    </span>
                </button>
                <button
                  class="btn btn-danger"
                  :disabled="!exists"
                  @click="exists && deleteAllNews"
                >Delete All News</button>
              </div>
  
              <!-- NO KEY SAVED → show input + Save -->
              <div v-if="!exists">
                <div class="mb-3">
                  <label for="apiKeyInput" class="form-label">API Key</label>
                  <input
                    id="apiKeyInput"
                    v-model="apiKeyInput"
                    type="text"
                    class="form-control"
                    placeholder="Enter your NewsAPI key"
                  />
                </div>
                <button
                  class="btn btn-outline-secondary d-block mx-auto mt-3"
                  @click="saveApiKey"
                >Save API Key</button>
              </div>
  
              <!-- KEY EXISTS → show 20-star mask + Delete -->
              <div v-else>
                <div class="mb-3">
                  <label class="form-label">API Key</label>
                  <div class="input-group">
                    <input
                      type="text"
                      class="form-control"
                      :value="starMask"
                      readonly
                    />
                    <button
                      class="btn btn-outline-danger"
                      @click="destroyApiKey"
                    >Delete API Key</button>
                  </div>
                </div>
              </div>
  
            </div>
          </div>
  
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  import Swal from 'sweetalert2'
  
  export default {
    name: 'ControlComponent',
    data() {
      return {
        apiKeyInput: '',   // only for the input when no key saved
        exists: false,     // true once backend confirms a key is stored
        creating: false,
        dotCount: 0,          // for cycling “.” → “..” → “...”
        dotTimer: null,
      }
    },
    computed: {
      // fixed 20-star mask
      starMask() {
        return '*'.repeat(20)
      },
      ellipsis() {
      return '.'.repeat(this.dotCount)
    }
    },
    methods: {
      // check if a key exists (no key value returned)
      async checkApiKey() {
        try {
          const { data } = await axios.post('/news/settings')
          this.exists = data.exists
          if (!this.exists) {
            await Swal.fire({
              icon: 'warning',
              title: 'API Key Missing',
              text: 'Please input your NewsAPI key and save it.',
              allowOutsideClick: false,
            })
          }
        } catch (e) {
          console.error(e)
        }
      },
  
      visitNews() {
        window.location.href = '/news'
      },
  
      createNews() {
        // your import logic…
        console.log('Creating news...')
      },
  
      deleteAllNews() {
        Swal.fire({
          title: 'Are you sure?',
          text: 'This will delete all news.',
          icon: 'warning',
          showCancelButton: true,
        }).then(r => {
          if (r.isConfirmed) {
            axios.post('/news/delete-all')
              .then(() => Swal.fire('Deleted','All news deleted','success'))
              .catch(() => Swal.fire('Error','Failed to delete news','error'))
          }
        })
      },
  
      // save only what's in apiKeyInput
      async saveApiKey() {
        const key = this.apiKeyInput.trim()
        if (!key) {
          return Swal.fire('API Key Required','Please enter your key.','warning')
        }
        try {
          const res = await axios.post('/news/api-save', { api_key: key })
          await Swal.fire('Saved', res.data.message, 'success')
          this.apiKeyInput = ''   // clear it
          this.checkApiKey()      // flip exists=true
        } catch (err) {
          if (err.response?.status === 422) {
            return Swal.fire('Validation Error',
              err.response.data.errors.api_key.join('\n'), 'error')
          }
          if (err.response?.status === 401) {
            return Swal.fire('Invalid Key', err.response.data.error, 'error')
          }
          Swal.fire('Error','Could not save key','error')
        }
      },
  
      // delete the saved key
      async destroyApiKey() {
        const r = await Swal.fire({
          title: 'Remove API Key?',
          text: 'This will delete your saved key.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, delete it',
        })
        if (!r.isConfirmed) return
  
        try {
          await axios.post('/news/api-delete')
          this.exists = false
          Swal.fire('Deleted','API key removed','success')
        } catch {
          Swal.fire('Error','Failed to delete API key','error')
        }
      },
      async generateNews() {
      this.startDots()
      this.creating = true

      try {
        const res = await axios.post('/news/generate')
        await Swal.fire('Done','News have been created successfully','success')
      } catch (err) {
        Swal.fire('Error','Failed to create news','error')
      } finally {
        this.stopDots()
        this.creating = false
      }
    },

    // helpers for ellipsis animation
    startDots() {
      this.dotCount = 0
      this.dotTimer = setInterval(() => {
        this.dotCount = (this.dotCount + 1) % 4  // cycles 0→1→2→3→0
      }, 400)
    },
    stopDots() {
        clearInterval(this.dotTimer)
        this.dotCount = 0
        },
    },
    mounted() {
      this.checkApiKey()
    }
  }
  </script>
  