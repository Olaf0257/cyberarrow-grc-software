import { createAction, createReducer } from '@reduxjs/toolkit'
import { fetchCalendarMorePopoverData, resetCalendarMorePopoverData } from '../../actions/global-dashboard/calendar'


const initialState = {
    status: 'idel',
    calendarEvents: [],
    pageCount: null,
    currentPage: 0,
    totalCount: null,
    loading: false
}

const calendarMorePopoverReducer = createReducer(initialState, (builder) => {
  builder
    .addCase(fetchCalendarMorePopoverData.fulfilled, (state, action) => {
        if (action.payload.success) {
            let {calendarTasks,currentPage, pageCount } = action.payload.data
            let newTasks = calendarTasks.map(task=> JSON.parse(task))

            state.calendarEvents = [...state.calendarEvents, ...newTasks ]
            state.currentPage = currentPage
            state.pageCount = pageCount
        }
        state.loading= false;
    })
      .addCase(fetchCalendarMorePopoverData.pending, (state) => {
          state.loading = true;
      })
    .addCase(resetCalendarMorePopoverData, (state) => {
        return initialState
    })
})

export default calendarMorePopoverReducer
