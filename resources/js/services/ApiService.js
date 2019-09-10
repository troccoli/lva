import axios from "axios";

const apiClient = axios.create({
  baseURL        : "/api/v1",
  withCredentials: false,
  headers        : {
    Accept        : "application/json",
    "Content-Type": "application/json"
  }
});

export default {
  getSeasons() {
    return apiClient.get("/seasons");
  },
  getCompetitions(seasonId) {
    return apiClient.get("/competitions?season=" + seasonId);
  },
  getDivisions(competitionId) {
    return apiClient.get("/divisions?competition=" + competitionId)
  },
  getFixtures(divisionId, limit, page) {
    return apiClient.get("/fixtures?division=" + divisionId + "&page=" + page + '&perPage=' + limit);
  },
};
