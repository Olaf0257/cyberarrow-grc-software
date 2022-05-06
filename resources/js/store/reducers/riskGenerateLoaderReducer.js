import { createAction, createReducer } from '@reduxjs/toolkit'

const show = createAction('riskGenerateLoader/show')
const hide = createAction('riskGenerateLoader/hide')

const initialState = {
    show: false
}

const riskGenerateLoaderReducer = createReducer(initialState, (builder) => {
  builder
    .addCase(show, (state, action) => {
      state.show = true
    })
    .addCase(hide, (state, action) => {
        state.show = false
    })
})

export default riskGenerateLoaderReducer;
