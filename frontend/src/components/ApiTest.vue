<script>
export default {
  data: () => ({
    apiResponse: null,
    modalData: "",
    dayName: "",
  }),

  created() {
    this.fetchData();
    this.getDate();
  },

  methods: {
    async fetchData() {
      this.apiResponse = JSON.parse(localStorage.getItem("usersData"));
      if (
        this.apiResponse &&
        this.apiResponse.currentDate &&
        new Date(this.apiResponse.currentDate).getTime() >= new Date().getTime()
      ) {
        console.log("from local userdata");
        this.apiResponse = JSON.parse(localStorage.getItem("usersData"));
      } else {
        console.log("live api call ");
        // remove Local storage data
        localStorage.removeItem("usersData");
        const url = 'http://localhost/'
        this.apiResponse = await (await fetch(url)).json();
        if (this.apiResponse) {
          localStorage.setItem("usersData", JSON.stringify(this.apiResponse));
        }
      }
    },
    async getData(data) {
      this.modalData = data;
    },
    getDate() {
      var date = new Date();
      this.dayName = date.toLocaleDateString("en-EN", { weekday: "long" });
      let day = date.getDate();
      let month = date.toLocaleDateString("en-EN", { month: "long" });
      let year = date.getFullYear();
      return day + " " + month + ", " + year;
    },
  },
};
</script>
<template>
  <div v-if="!apiResponse">Pinging the api...</div>
  <div
    class="modal fade"
    id="weatherModal"
    tabindex="-1"
    aria-labelledby="weatherModalLabel"
    aria-hidden="true"
    >
    <div 
    class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="weatherModalLabel">Weather Report</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <div class="card card-weather">
            <div class="card-body">
              <div class="weather-date-location">
                <h3>{{ this.dayName ? this.dayName : "" }}</h3>
                <p class="text-gray">
                  <span class="weather-date"> {{ this.getDate() }}</span>
                </p>
                <p class="text-gray">
                  <span class="weather-location"
                    >({{
                      this.modalData?.latitude ? this.modalData?.latitude : ""
                    }},
                    {{
                      this.modalData?.longitude ? this.modalData?.longitude : ""
                    }})</span
                  >
                </p>
              </div>
              <div class="weather-data d-flex">
                <div class="mr-auto">
                  <h4 class="display-3">
                    {{
                      this.modalData?.main?.temp ? this.modalData?.main?.temp : ""
                    }}
                    <span class="symbol">&deg;</span>C
                  </h4>
                  <p>
                    {{
                      this.modalData?.weather?.main
                        ? this.modalData?.weather?.main
                        : ""
                    }}
                    ({{
                      this.modalData?.weather?.description
                        ? this.modalData?.weather?.description
                        : ""
                    }})
                  </p>
                </div>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="d-flex weakly-weather">
                <div class="weakly-weather-item">
                  <p class="mb-0">Temprature Max</p>
                  <i class="mdi mdi-weather-cloudy"></i>
                  <p class="mb-0">
                    {{
                      this.modalData?.main?.temp_max
                        ? this.modalData?.main?.temp_max
                        : ""
                    }}&deg;
                  </p>
                </div>
                <div class="weakly-weather-item">
                  <p class="mb-1">Temprature Min</p>
                  <i class="mdi mdi-weather-hail"></i>
                  <p class="mb-0">
                    {{
                      this.modalData?.main?.temp_min
                        ? this.modalData?.main?.temp_min
                        : ""
                    }}&deg;
                  </p>
                </div>
              </div>
            </div>
          </div>
          <!--weather card ends-->
        </div>
      </div>
    </div>
  </div>
  <b-container fluid v-if="apiResponse">
    <b-row>
      <b-col>
        <b-table-simple responsive>
          <b-thead>
            <b-tr>
              <b-th sticky-column>ID</b-th>
              <b-th>Name</b-th>
              <b-th>Email</b-th>
              <b-th>Weather</b-th>
              <b-th>Action</b-th>
            </b-tr>
          </b-thead>
          <b-tbody>
            <b-tr v-for="row in apiResponse.users" :key="row.id">
              <b-td sticky-column variant="primary">{{ row.id }}</b-td>
              <b-td>{{ row.name }}</b-td>
              <b-td>{{ row.email }}</b-td>
              <b-td
                ><div
                  v-if="
                    !!row.weather &&
                    !!row.weather.main &&
                    row.weather.description
                  "
                >
                  {{ row.weather?.main }} ,
                  {{ row.weather?.description }}
                </div></b-td
              >
              <b-td>
                <b-button
                  variant="primary"
                  data-bs-toggle="modal"
                  data-bs-target="#weatherModal"
                  @click="getData(row)"
                  >Weather Report</b-button
                >
              </b-td>
            </b-tr>
          </b-tbody>
        </b-table-simple>
      </b-col>
    </b-row>
  </b-container>
</template>
