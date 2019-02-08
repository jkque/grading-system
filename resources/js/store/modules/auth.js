import axios from 'axios'
import Cookies from 'js-cookie'
import * as types from '../mutation-types'

// state
export const state = {
  user: null,
  token: Cookies.get('token'),
  school: null,
  roles: null
}

// getters
export const getters = {
  user: state => state.user,
  token: state => state.token,
  check: state => state.user !== null,
  school: state => state.school,
  roles: state => state.roles
}

// mutations
export const mutations = {
  [types.SAVE_TOKEN] (state, { token, remember }) {
    state.token = token
    Cookies.set('token', token, { expires: remember ? 365 : null })
  },

  [types.FETCH_USER_SUCCESS] (state, { user }) {
    state.user = user
    state.roles = user.roles.map(role => role.name)
  },

  [types.FETCH_USER_FAILURE] (state) {
    state.token = null
    Cookies.remove('token')
  },

  [types.LOGOUT] (state) {
    state.user = null
    state.roles = null;
    state.school = null;
    state.token = null

    Cookies.remove('token')
  },

  [types.UPDATE_USER] (state, { user }) {
    state.user = user
    state.roles = user.roles.map(role => role.name)
  },

  [types.FETCH_USER_SCHOOL_SUCCESS] (state, { school }) {
    state.school = school
  },

  [types.UPDATE_USER_SCHOOL] (state, { school }) {
    state.school = school
  }
}

// actions
export const actions = {
  saveToken ({ commit, dispatch }, payload) {
    commit(types.SAVE_TOKEN, payload)
  },

  async fetchUser ({ commit }) {
    try {
      const { data } = await axios.get('/api/user')

      commit(types.FETCH_USER_SUCCESS, { user: data })
    } catch (e) {
      commit(types.FETCH_USER_FAILURE)
    }
  },

  updateUser ({ commit }, payload) {
    commit(types.UPDATE_USER, payload)
  },

  async logout ({ commit }) {
    try {
      await axios.post('/api/logout')
    } catch (e) { }

    commit(types.LOGOUT)
  },

  async fetchOauthUrl (ctx, { provider }) {
    const { data } = await axios.post(`/api/oauth/${provider}`)

    return data.url
  },

  async fetchSchool ({ commit }) {
    try {
      const { data } = await axios.get('/api/user/school')

      commit(types.FETCH_USER_SCHOOL_SUCCESS, { school: data })
    } catch (e) {
      commit(types.FETCH_USER_SCHOOL_FAILURE)
    }
  },

  updateSchool ({ commit }, payload) {
    commit(types.UPDATE_USER_SCHOOL, payload)
  },
}
