import { createAction, createReducer } from '@reduxjs/toolkit'
import { pageLoaderUpdate } from '../../actions/page-loader'

const initialState = {
    status: 'idel'
}

const pageLoaderReducer = createReducer(initialState, (builder) => {
  builder
    .addCase(pageLoaderUpdate, (state, action) => {
        state.status = action.payload
    })
})

export default pageLoaderReducer
