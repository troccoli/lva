import Vue from 'vue';
import Router from "vue-router";
import FixturesList from "./views/FixturesList";
import FixtureCreate from "./views/FixtureCreate";

Vue.use(Router);

export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/fixtures',
      name: 'fixtures.index',
      component: FixturesList,
    },
    {
      path: '/fixtures/create',
      name: 'fixtures.create',
      component: FixtureCreate
    }
  ]
})
