import { createAction, createReducer } from '@reduxjs/toolkit'

const show = createAction('reportGenerateLoader/show')
const hide = createAction('reportGenerateLoader/hide')

const initialState = {
    show: false
}

const reportGenerateLoaderReducer = createReducer(initialState, (builder) => {
  builder
    .addCase(show, (state, action) => {
      state.show = true
    })
    .addCase(hide, (state, action) => {
        state.show = false
    })
})

export default reportGenerateLoaderReducer
