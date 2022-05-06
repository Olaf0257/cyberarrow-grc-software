import { combineReducers } from 'redux'
import projectReducer from './projectReducer'
import { default as dashboardCalendarReducer } from './dashboard/calendarReducer'
import { default as dashboardCalendarMorePopoverReducer } from './dashboard/calendarMorePopoverReducer'

export default combineReducers({
    projectReducer,
    dashboardCalendarReducer,
    dashboardCalendarMorePopoverReducer
})
