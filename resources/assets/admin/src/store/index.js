import Vue from 'vue'
import Vuex from 'vuex'
import Users from '@ac/Users/store'
import Category from '@ac/Category/store'
import Projects from '@ac/Projects/store'
import List from '@ac/InProject/List/store'
import Discussion from '@ac/InProject/Discussion/store'
import Milestone from '@ac/InProject/Milestone/store'

Vue.use(Vuex)

export default new Vuex.Store({
    root: true,
    state: {
        isAuthenticated: false,
        snackbar: {},
        status: '',
        hasLoadedOnce: false
    },
    getters: {
        snackbar: state => state.snackbar,
        isAuthenticated: state => !!state.access_token
    },
    mutations: {
        setSnackbar (state, payload) {
            state.snackbar = payload
        },
        setSnackbarHide (state) {
            state.snackbar.show = !state.snackbar.show
        },
        auth (state, payload) {
            state.isAuthenticated = payload
        }
    },
    actions: {
        authRequest: ({ commit }, payload) => {
            let remember = payload.remember ? payload.remember : false
            let data = {
                'email': payload.email,
                'password': payload.password,
                'remember': remember
            }
            return new Promise((resolve, reject) => {
                axios.post('/login', data)
                    .then(() => {
                        commit('auth', true)
                        resolve()
                    })
                    .catch((err) => {
                        reject(err)
                    })
            })
        },
        authLogout: ({ commit }) => {
            return new Promise((resolve, reject) => {
                axios.post('/logout')
                    .then(() => {
                        commit('auth', false)
                        resolve()
                    })
                    .catch((err) => {
                        reject(err)
                    })
            })
        }
    },
    modules: {
        Users,
        Category,
        Projects,
        List,
        Discussion,
        Milestone
    }
})
