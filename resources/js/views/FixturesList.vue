<template>
    <v-container>
        <v-row>
            <v-col class="text-left"><h1>Fixtures</h1></v-col>
            <v-col class="text-right">
                <v-btn color="primary" fab aria-label="Add a fixture" :to="{name: 'fixtures.create'}" dusk="add">
                    <v-icon>mdi-plus</v-icon>
                </v-btn>
            </v-col>
        </v-row>
        <v-row>
            <v-col dusk="season-selector">
                <v-select :items="seasons"
                          label="Season"
                          v-model="season"
                          outlined
                />
            </v-col>
            <v-col dusk="competition-selector">
                <v-select :items="competitions"
                          label="Competition"
                          v-model="competition"
                          outlined
                />
            </v-col>
            <v-col dusk="division-selector">
                <v-select :items="divisions"
                          label="Division"
                          v-model="division"
                          outlined
                />
            </v-col>
        </v-row>
        <v-row>
            <v-col dusk="list">
                <v-data-table
                        :headers="headers"
                        :items="fixtures"
                        item-key="id"
                        :loading="loading"
                        loading-text="Loading fixtures. Please wait."
                        no-data-text="There are no fixtures yet."
                >
                </v-data-table>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
  export default {
    name      : "FixturesList",
    data() {
      return {
        limit      : null,
        season     : null,
        competition: null,
        division   : null,
        headers    : [
          {text: 'Division', value: 'division', sortable: false},
          {text: 'Home team', value: 'home_team'},
          {text: 'Away team', value: 'away_team'},
        ],
        loading    : false,
      }
    },
    created() {
      this.loading = true;
      this.$store.dispatch("fetchSeasons")
        .then(store => {
          if (store.state.seasons.length) {
            this.season = store.state.seasons[0].id;
          } else {
            this.loading = false;
          }
      });
    },
    watch     : {
      season     : function () {
        this.loading = true;
        this.$store.dispatch("fetchCompetitions", {
          seasonId: this.season
        }).then(store => {
          if (store.state.competitions.length) {
            this.competition = store.state.competitions[0].id;
          } else {
            this.loading = false;
          }
        });
      },
      competition: function () {
        this.loading = true;
        this.$store.dispatch("fetchDivisions", {
          competitionId: this.competition
        }).then(store => {
          if (store.state.divisions.length) {
            this.division = store.state.divisions[0].id;
          } else {
            this.loading = false;
          }
        });
      },
      division   : function () {
        this.loading = true;
        this.$store.dispatch("fetchFixtures", {
          divisionId: this.division
        }).then(() => {
          this.loading = false;
        })
      },
    },
    computed  : {
      seasons() {
        if (this.$store.state.seasons.length === 0) {
          return [
            {
              value: null,
              text : 'No seasons'
            }
          ];
        }

        return this.$store.state.seasons
          .map(function (season) {
            return {
              value: season.id,
              text : season.name
            }
          });
      },
      competitions() {
        if (this.$store.state.competitions.length === 0) {
          return [
            {
              value: null,
              text : 'No competitions'
            }
          ];
        }

        return this.$store.state.competitions
          .map(function (competition) {
            return {
              value: competition.id,
              text : competition.name
            }
          })
      },
      divisions() {
        if (this.$store.state.divisions.length === 0) {
          return [
            {
              value: null,
              text : 'No divisions'
            }
          ];
        }

        return this.$store.state.divisions
          .map(function (division) {
            return {
              value: division.id,
              text : division.name
            }
          })
      },
      fixtures() {
        if (this.loading || this.$store.state.fixtures.length === 0) {
          return [];
        }

        return this.$store.state.fixtures;
      }
    },
  }
</script>

<style scoped>
    a.v-btn:hover {
        text-decoration: none;
    }
</style>
