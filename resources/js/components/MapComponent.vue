<template>
  <div class="position-relative">
    <div class="w-100 p-3 position-absolute" style="z-index: 9;">
      <div class="row">
        <div class="col-md-6 col-lg-5 col-xl-4">
          <div class="input-group">
            <input
              v-model="search"
              type="text"
              class="form-control border-end-0"
              @keydown="debounceSearch"
              @keydown.enter="doSearch"
            >
            <button class="input-group-text bg-light border-start-0" @click.left="doSearch">
              <font-awesome-icon icon="fa-solid fa-magnifying-glass"/>
            </button>
          </div>

        </div>
      </div>
    </div>
    <GoogleMap
      :api-key="map_key"
      style="width: 100%; height: 100vh;"
      :center="center"
      :zoom="15"
      :fullscreen-control="false"
      :map-type-control="false"
    >
      <Marker :options="{ position: center }"/>
    </GoogleMap>
  </div>
</template>

<script setup>
import { GoogleMap, Marker } from 'vue3-google-map'
import { defineComponent, onMounted, ref, watch } from 'vue'

const props = defineProps({
  searchApi: String,
})

defineComponent({
  GoogleMap,
  Marker,
})

const map_key = import.meta.env.VITE_GOOGLE_MAP_KEY
const center = { lat: 40.689247, lng: -74.044502 }

const search = ref('Bang sue')
const next_page_token = ref('')

const data = ref([])

onMounted(() => {
  doSearch()
})

const debounceSearch = _.debounce(doSearch, 700)

function doSearch () {
  if (search.value) {
    axios.get(props.searchApi, {
      params: {
        query: search.value,
        next_page_token: next_page_token.value,
      },
    })
      .then(({ data }) => {
        next_page_token.value = data.next_page_token || ''
        data.value.push(...data.results)
      })
  }
}

watch(() => search, (search) => {
  next_page_token.value = ''

  if (!search) {
    data.value = []
  }
})

</script>
