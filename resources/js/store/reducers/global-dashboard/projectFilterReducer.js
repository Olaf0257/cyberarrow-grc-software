import { createAction, createReducer } from '@reduxjs/toolkit'
import {fetchProjectFilterData, updateSelectedProjects} from '../../actions/global-dashboard/project-filter'


const initialState = {
    status: 'idel',
    projects: [],
    selectedProjects: []
}

const projectFilterReducer = createReducer(initialState, (builder) => {
  builder
    .addCase(fetchProjectFilterData.fulfilled, (state, action) => {
        if (action.payload.success) {
            state.projects = action.payload.data
        }
    })
    .addCase(updateSelectedProjects, (state, action) => {
        state.selectedProjects = action.payload
    })
})

export default projectFilterReducer
