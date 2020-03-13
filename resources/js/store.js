import Vue from 'vue'
import Vuex from 'vuex'
import ApiService from "./services/ApiService";

Vue.use(Vuex);

export default new Vuex.Store({
  state    : {
    seasons     : [],
    competitions: [],
    divisions   : [],
    fixtures    : [],
  },
  mutations: {
    SET_SEASONS(state, seasons) {
      state.seasons = seasons;
    },
    SET_COMPETITIONS(state, competitions) {
      state.competitions = competitions;
    },
    SET_DIVISIONS(state, divisions) {
      state.divisions = divisions;
    },
    SET_FIXTURES(state, fixtures) {
      state.fixtures = fixtures;
    },
  },
  actions  : {
    fetchSeasons({commit}) {
      return ApiService.getSeasons()
        .then(response => {
          commit("SET_SEASONS", response.data.data);
          return this;
        })
        .catch(error => {
          console.log('There was an error: ', error.response);
        });
    },
    fetchCompetitions({commit}, {seasonId}) {
      return ApiService.getCompetitions(seasonId)
        .then(response => {
          commit("SET_COMPETITIONS", response.data.data);
          return this;
        })
        .catch(error => {
          console.log('There was an error: ', error.response);
        });
    },
    fetchDivisions({commit}, {competitionId}) {
      return ApiService.getDivisions(competitionId)
        .then(response => {
          commit("SET_DIVISIONS", response.data.data);
          return this;
        })
        .catch(error => {
          console.log('There was an error: ', error.response);
        });
    },
    fetchFixtures({commit}, {divisionId}) {
      return ApiService.getFixtures(divisionId)
        .then(response => {
          commit("SET_FIXTURES", response.data.data);
          return this;
        })
        .catch(error => {
          console.log('There was an error: ', error.response);
        });
    },
  }
})
