import { combineReducers } from 'redux'
import projectFilterReducer from './projectFilterReducer'
import calendarReducer from './calendarReducer'
import calendarMorePopoverReducer from './calendarMorePopoverReducer'

export default combineReducers({
    projectFilterReducer,
    calendarReducer,
    calendarMorePopoverReducer
})
