import { combineReducers } from 'redux'

import dataScopeReducer from './dataScopeReducer'
import commonReducer from './common'
import globalDashboardReducer from './global-dashboard'
import policyManagement from './policy-management'
import pageLoaderReducer from './page-loader'
import complianceReducer from './compliance'
import reportGenerateLoaderReducer from './reportGenerateLoaderReducer'
import riskGenerateLoaderReducer from "./riskGenerateLoaderReducer";

const rootReducer = combineReducers({
  // Define a top-level state field named `todos`, handled by `todosReducer`
  appDataScope: dataScopeReducer,
  policyManagement: policyManagement,
  commonReducer: commonReducer,
  globalDashboardReducer: globalDashboardReducer,
  pageLoaderReducer: pageLoaderReducer,
  complianceReducer: complianceReducer,
  reportGenerateLoaderReducer: reportGenerateLoaderReducer,
  riskGenerateLoaderReducer: riskGenerateLoaderReducer,
})

export default rootReducer
