import { createAction, createReducer } from '@reduxjs/toolkit'
import { fetchDepartmentFilterData,updateSelectedDepartments } from '../../actions/common/department-filter'


const initialState = {
    status: 'idel',
    departmentTreeData: [],
    selectedDepartment: []
}

const departmentFilterReducer = createReducer(initialState, (builder) => {
  builder
    .addCase(fetchDepartmentFilterData.fulfilled, (state, action) => {
        if (action.payload.success) {
            state.departmentTreeData = action.payload.data
        }
    })
    .addCase(updateSelectedDepartments, (state, action) => {
        state.selectedDepartment = action.payload
    })
})

export default departmentFilterReducer
