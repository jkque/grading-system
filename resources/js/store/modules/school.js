import axios from 'axios'
import Cookies from 'js-cookie'
import * as types from '../mutation-types'

// state
export const state = {
    list: null,
}

// getters
export const getters = {
    list: state => state.school,
}

// mutations
export const mutations = {
  [types.FETCH_USER_SCHOOL_SUCCESS] (state, { list }) {
    state.list = list
  },

  [types.UPDATE_USER_SCHOOL] (state, { list }) {
    state.list = list
  }
}

// actions
export const actions = {

//   async fetchSchool ({ commit }) {
//     try {
//       const { data } = await axios.get('/api/user/school')

//       commit(types.FETCH_USER_SCHOOL_SUCCESS, { school: data })
//     } catch (e) {
//       commit(types.FETCH_USER_SCHOOL_FAILURE)
//     }
//   },
}

