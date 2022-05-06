import { createAction, createReducer } from '@reduxjs/toolkit'
import { fetchCalendarData } from '../../../actions/compliance/dashboard'


const initialState = {
    status: 'idel',
    calendarEvents: []
}

const calendarReducer = createReducer(initialState, (builder) => {
  builder
    .addCase(fetchCalendarData.fulfilled, (state, action) => {

        if (action.payload.success) {
            let {calendarTasks} = action.payload.data

            state.calendarEvents = calendarTasks.map(task=> JSON.parse(task))
        }
    })
})

export default calendarReducer
