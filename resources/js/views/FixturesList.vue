<template>
    <v-container>
        <v-row>
            <v-col class="text-left"><h1>Fixtures</h1></v-col>
            <v-col class="text-right">
                <router-link :to="{name: 'fixtures.create'}" class="btn btn-primary btn-sm">New fixture</router-link>
            </v-col>
        </v-row>
        <v-row>
            <v-col>
                <BaseSelect dusk="season-selector"
                            label="Select a season"
                            :options="seasons"
                            v-model="season"
                />
            </v-col>
            <v-col>
                <BaseSelect dusk="competition-selector"
                            label="Select a competition"
                            :options="competitions"
                            v-model="competition"
                />
            </v-col>
            <v-col>
                <BaseSelect dusk="division-selector"
                            label="Select a division"
                            :options="divisions"
                            v-model="division"
                />
            </v-col>
        </v-row>
        <v-row>
            <div id="resources-list" class="table" dusk="list">
                <div v-if="fixtures.length === 0" class="alert alert-warning">
                    <h4 class="alert-heading">Whoops!</h4>
                    <p class="mb-0">There are no fixtures yet.</p>
                </div>
                <table v-else class="table table-bordered table-hover table-sm">
                    <thead>
                    <tr>
                        <th>Division</th>
                        <th>Home team</th>
                        <th>Away team</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="fixture in fixtures" :key="fixture.id" :dusk="'fixture-' + fixture.id + '-row'">
                        <td>{{ fixture.division }}</td>
                        <td>{{ fixture.home_team }}</td>
                        <td>{{ fixture.away_team }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </v-row>
        <v-row>
            <button @click="goToPage(page-1)">Previous page</button>
            &nbsp;|&nbsp;
            <button @click="goToPage(page+1)">Next page</button>
        </v-row>
    </v-container>
</template>

<script>
  import BaseSelect from "../components/BaseSelect";

  export default {
    name      : "FixturesList",
    components: {BaseSelect},
    data() {
      return {
        limit      : 10,
        season     : null,
        competition: null,
        division   : null,
        page       : 1,
      }
    },
    created() {
      this.$store.dispatch("fetchSeasons")
        .then(store => {
          if (store.state.seasons.length) {
            this.season = store.state.seasons[0].id;
          }
        });
    },
    watch     : {
      season     : function () {
        this.$store.dispatch("fetchCompetitions", {
          seasonId: this.season
        }).then(store => {
          this.competition = store.state.competitions[0].id;
        });
      },
      competition: function () {
        this.$store.dispatch("fetchDivisions", {
          competitionId: this.competition
        }).then(store => {
          this.division = store.state.divisions[0].id;
        });
      },
      division   : function () {
        if (this.page === 1) {
          this.$store.dispatch("fetchFixtures", {
            divisionId: this.division,
            limit     : this.limit,
            page      : 1
          })
        } else {
          this.page = 1;
        }
      },
      page       : function () {
        this.$store.dispatch("fetchFixtures", {
          divisionId: this.division,
          limit     : this.limit,
          page      : this.page
        });
      }
    },
    computed  : {
      seasons() {
        if (this.$store.state.seasons.length === 0) {
          return [
            {
              value: null,
              label: 'No seasons'
            }
          ];
        }

        return this.$store.state.seasons
          .map(function (season) {
            return {
              value: season.id,
              label: season.name
            }
          });
      },
      competitions() {
        if (this.$store.state.competitions.length === 0) {
          return [
            {
              value: null,
              label: 'No competitions'
            }
          ];
        }

        return this.$store.state.competitions
          .map(function (competition) {
            return {
              value: competition.id,
              label: competition.name
            }
          })
      },
      divisions() {
        if (this.$store.state.divisions.length === 0) {
          return [
            {
              value: null,
              label: 'No divisions'
            }
          ];
        }

        return this.$store.state.divisions
          .map(function (division) {
            return {
              value: division.id,
              label: division.name
            }
          })
      },
      fixtures() {
        if (this.$store.state.fixtures.length === 0) {
          return [];
        }

        return this.$store.state.fixtures;
      }
    },
    methods   : {
      goToPage(page) {
        this.page = page;
      }
    }
  }
</script>

<style scoped>

</style>
