import { combineReducers } from 'redux'
import campaignReducer from "./campaignReducer"
import campaignDuplicateReducer from "./campaignDuplicateReducer"

export default combineReducers({
    campaignReducer,
    campaignDuplicateReducer
})
