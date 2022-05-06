import { createAction, createReducer } from '@reduxjs/toolkit'
import { fetchProjectList } from '../../actions/compliance/project'

const initialState = {
    loading: false,
    projects: [],
}

const projectReducer = createReducer(initialState, (builder) => {
  builder
    .addCase(fetchProjectList.pending, (state, action) => {
        state.loading = true
    })
    .addCase(fetchProjectList.fulfilled, (state, action) => {
        state.projects = action.payload
        state.loading = false
    })
    .addCase(fetchProjectList.rejected, (state, action) => {
        state.loading = false
    })
})

export default projectReducer
