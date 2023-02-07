<template>
  <div class="position-relative">
    <div class="w-100 p-3 position-absolute">
      <div class="row">
        <div class="col-md-6 col-lg-5 col-xl-4 position-relative">
          <div class="position-absolute w-fit" style="z-index: 9;">
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

      <div v-if="data.length" class="row">
        <div class="col-md-6 col-lg-5 col-xl-4 position-relative">
          <div class="restaurent-list">
            <div class="restaurent-container">
              <div class="row g-0">
                <template v-for="restaurent in data">
                  <div class="col-11 col-sm-8 col-md-12 border-end border-end-md-0 border-top-md">
                    <div class="p-3">
                      <div class="mb-2">
                        <span class="h5">{{ restaurent.name }}</span>
                        <span class="small text-secondary">
                          (
                          <font-awesome-icon icon="fa-solid fa-star"/>
                          {{ restaurent.rating }}
                          )
                        </span>
                      </div>

                      <div class="d-flex">
                        <div>
                          <img
                            v-if="restaurent.photos"
                            :src="`https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photo_reference=${ restaurent.photos[0].photo_reference }&key=${ map_key }`"
                            alt="restaurent image"
                          >
                          <img v-else src="../../images/no-img.png" alt="no image">
                        </div>

                        <div class="w-100 ms-2">
                          <div class="text-secondary">
                            {{ restaurent.formatted_address }}
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </template>

                <template v-if="next_page_token">
                  <div class="col-4 col-md-12">
                    <scroll-load @enterView="doSearch"/>
                  </div>
                </template>
              </div>
            </div>
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
      <template v-for="marker in data">
        <Marker :options="{ position: marker.geometry.location }"/>
      </template>
    </GoogleMap>
  </div>
</template>

<script setup>
import { GoogleMap, Marker } from 'vue3-google-map'
import { defineComponent, onMounted, ref, watch } from 'vue'
import ScrollLoad from './ScrollLoad.vue'

const props = defineProps({
  searchApi: String,
})

defineComponent({
  GoogleMap,
  Marker,
})

const map_key = import.meta.env.VITE_GOOGLE_MAP_KEY
const center = ref({ lat: 13.754553, lng: 100.492867 })

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
      .then((response) => {
        if (response.data.results.length) {
          if (!data.value.length) {
            center.value = response.data.results[0].geometry.location
          }

          next_page_token.value = response.data.next_page_token || ''
          data.value?.push(...response.data.results)
        }
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

<style lang="scss">
@import 'bootstrap/scss/_functions.scss';
@import 'bootstrap/scss/_variables.scss';
@import 'bootstrap/scss/_mixins.scss';

.w-fit {
  width: calc(100% - 24px);
}

@include media-breakpoint-up(md) {
  .restaurent-list {
    position: absolute;
    z-index: 8;
    width: calc(100% + 8px);
    height: 100vh;
    left: -4px;
    top: -16px;
    background-color: white;
    padding: 70px 12px 12px;

    & .restaurent-container {
      max-height: 100%;
      overflow-y: auto;

      & img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
      }
    }
  }
}

@include media-breakpoint-down(md) {
  .restaurent-list {
    position: absolute;
    z-index: 8;
    width: calc(100% + 8px);
    height: 220px;
    left: -4px;
    top: calc(100vh - 220px - 16px);
    background-color: white;
    padding: 12px;

    & .restaurent-container {
      max-height: 100%;
      overflow-x: auto;

      & .row {
        flex-wrap: nowrap !important;
      }

      & img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
      }
    }
  }
}
</style>
