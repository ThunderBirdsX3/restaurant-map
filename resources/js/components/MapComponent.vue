<template>
  <div class="position-relative">
    <!--input search-->
    <div class="w-100 p-3 position-absolute">
      <div class="row">
        <div class="col-md-6 col-lg-5 col-xl-4 col-xxl-3 position-relative">
          <div class="position-absolute w-fit" style="z-index: 9;">
            <div class="input-group">
              <input
                v-model="search"
                type="text"
                class="form-control border-end-0"
                @keydown="debounceSearch"
                @keydown.enter="doSearch"
              >
              <button class="input-group-text bg-light border-start-0" @click.left.exact="doSearch">
                <font-awesome-icon icon="fa-solid fa-magnifying-glass"/>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--input search-->

    <div class="row justify-content-end flex-md-row-reverse g-0">
      <!--GoogleMap-->
      <div
        :class="{
          'px-0': true,
          'col-12': !data.length,
          'col-md-6 col-lg-7 col-xl-8 col-xxl-9': data.length,
        }"
      >
        <GoogleMap
          :api-key="map_key"
          :center="center"
          :zoom="zoom"
          :fullscreen-control="false"
          :map-type-control="false"
          class="map-content"
        >
          <template v-for="marker in data">
            <Marker :options="{ position: marker.geometry.location }"/>
          </template>
        </GoogleMap>
      </div>
      <!--GoogleMap-->

      <!--data list-->
      <div v-if="data.length" class="col-md-6 col-lg-5 col-xl-4 col-xxl-3 px-0">
        <div class="restaurant-list">
          <div class="restaurant-container">
            <div class="row g-0">
              <template v-for="restaurant in data">
                <div
                  class="col-11 col-sm-8 col-md-12 border-end border-end-md-0 border-top-md cursor-pointer"
                  @click.exact="panTo(restaurant.geometry.location)"
                >
                  <div class="p-3">
                    <div class="mb-2">
                      <span class="h5">{{ restaurant.name }}</span>
                      <span class="small text-secondary">
                        (
                        <font-awesome-icon icon="fa-solid fa-star"/>
                        {{ restaurant.rating }}
                        )
                      </span>
                    </div>

                    <div class="d-flex">
                      <div>
                        <img
                          v-if="restaurant.photos"
                          :src="`https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photo_reference=${ restaurant.photos[0].photo_reference }&key=${ map_key }`"
                          alt="restaurant image"
                        >
                        <img v-else src="../../images/no-img.png" alt="no image">
                      </div>

                      <div class="w-100 ms-2">
                        <div class="text-secondary">
                          {{ restaurant.formatted_address }}
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </template>

              <template v-if="next_page_token">
                <div class="col-4 col-md-12">
                  <scroll-load @enterView="doSearch(false)"/>
                </div>
              </template>
            </div>
          </div>
        </div>
      </div>
      <!--data list-->
    </div>
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
const zoom = ref(15)

const search = ref('Bang sue')
const next_page_token = ref('')

const data = ref([])

onMounted(() => {
  doSearch()
})

// ??????????????????????????????????????? api ????????????????????????????????????????????????????????????
const debounceSearch = _.debounce(doSearch, 700)

function doSearch (new_search = true) {
  if (search.value) {
    // ???????????????????????? ???????????????????????? next_page_token ????????????????????????????????????????????????????????????
    // ????????? set next_page_token ????????? data ?????????????????????????????????
    if (!next_page_token.value || new_search) {
      next_page_token.value = null
      data.value = []
    }

    axios.get(props.searchApi, {
      params: {
        query: search.value,
        next_page_token: next_page_token.value,
      },
    })
      .then((response) => {
        // ?????????????????????????????????????????????????????????
        if (response.data.results.length) {
          // ???????????????????????????????????????????????? load ?????????????????????????????? ????????? pan ??????????????????????????????
          if (!data.value.length) {
            panTo(response.data.results[0].geometry.location, 15)
          }

          next_page_token.value = response.data.next_page_token || ''
          data.value?.push(...response.data.results)
        }
      })
  }
}

// ?????????????????????????????????????????????????????????????????????????????????
function panTo (location, zoom_to = 18) {
  center.value = location
  zoom.value = zoom_to
}

// ?????????????????????????????????????????????????????????????????????????????? ?????????????????????????????????????????????????????????????????? ?????????????????????????????? next_page_token
watch(search, () => {
  next_page_token.value = ''
})

</script>

<style lang="scss">
@import 'bootstrap/scss/_functions.scss';
@import 'bootstrap/scss/_variables.scss';
@import 'bootstrap/scss/_mixins.scss';

.w-fit {
  width: calc(100% - 28px);
}

.cursor-pointer {
  cursor: pointer;
}

@include media-breakpoint-up(md) {
  .map-content {
    width: 100%;
    height: 100vh;
  }

  .restaurant-list {
    height: 100vh;
    background-color: white;
    padding: 70px 16px 12px;

    & .restaurant-container {
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
  .map-content {
    width: 100%;
    height: calc(100vh - 220px);
  }

  .restaurant-list {
    height: 220px;
    left: -4px;
    background-color: white;
    padding: 16px;

    & .restaurant-container {
      max-height: 100%;
      overflow: auto;

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
