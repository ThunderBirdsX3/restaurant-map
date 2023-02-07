<template>
  <div class="d-flex justify-content-center">
    <div class="spinner-grow" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ScrollLoad',

  props: {
    threshold: {
      type: Number,
      default: 1,
    },
    once: {
      type: Boolean,
      default: false,
    },
  },

  methods: {
    enter () {
      this.$emit('enterView', this.unobserve)
    },

    leave () {
      this.$emit('leaveView', this.unobserve)
    },

    unobserve () {
      this.observer.unobserve(this.$el)
      this.observer = null
    },
  },

  mounted () {
    const options = {
      threshold: this.threshold,
    }

    this.observer = new IntersectionObserver(entries => {
      entries.forEach((entry) => {
        if (entry.isIntersecting && entry.intersectionRatio > 0) {
          this.enter()
          if (this.once) return this.unobserve()
        } else {
          this.leave()
        }
      })
    }, options)

    this.observer.observe(this.$el)
  },

  beforeDestroy () {
    if (this.observer) this.unobserve()
  },

  render (h) {
    return this.$slots.default ? this.$slots.default[0] : null
  },
}
</script>

<style scoped>

</style>
