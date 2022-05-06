import AppLayout  from '../layouts/app-layout/AppLayout';
import AuthLayout from '../layouts/auth-layout/AuthLayout';
import LoginPage from '../modules/auth/login/LoginPage';
import {default as ComplianceDashboard} from '../modules/compliance/dashboard/Dashboard';
import {default as ComplianceProjectListPage} from '../modules/compliance/project-list-page/ProjectListPage';
import {default as ComplianceProjectCreatePage} from '../modules/compliance/project-create-page/ProjectCreatePage';
import GlobalDashboard from '../modules/global-dashboard/GlobalDashboard';
import GlobalTaskMonitor from '../modules/global-task-monitor/GlobalTaskMonitor';
import {default as Dashboard} from '../modules/risk-management/dashboard/Dashboard';
import {default as RiskRegisterPage} from '../modules/risk-management/risk-register/RiskRegister';
import {default as RiskRegisterCreate} from '../modules/risk-management/risk-register/components/RiskRegisterCreate';
import {default as RiskRegisterShow} from '../modules/risk-management/risk-register/components/RiskRegisterShow';
import CampaignPage from '../modules/policy-management/campaign-page/CampaignPage';
import RiskSetup from '../modules/risk-management/risk-setup/RiskSetup';
import RiskSetupWizard from '../modules/risk-management/risk-setup/wizard/RiskSetupWizard';
import Controls from '../modules/controls/controls';
import ManualRiskSetup from '../modules/risk-management/risk-setup/manual/ManualRiskSetup';
import UserList from '../modules/user-management/components/UserList';
import UserCreatePage from '../modules/user-management/components/UserCreatePage';
import UserEditPage from '../modules/user-management/components/UserEditPage';
import AssetManagememt from "../modules/asset-management/AssetManagement";
import { Dashboard as KPIDashboard} from "../modules/controls/Dashboard";
import Integrations from "../modules/integrations/Integrations";

const routes = [
    {
        layout: AuthLayout,
        routes: [
            {
                path: '/testing',
                component: LoginPage,
            }
        ]
    },
    {
        layout: AppLayout,
        routes: [
            {
                path: '/global/dashboard',
                component: GlobalDashboard,
            },
            {
                path: '/global/tasks/:type',
                component: GlobalTaskMonitor,
            },
            /* Compliance module */
            {
                path: '/compliance/projects/view',
                component: ComplianceProjectListPage,
            },
            {
                path: '/compliance/dashboard',
                component: ComplianceDashboard,
            },
            {
                path: '/compliance/projects/create',
                component: ComplianceProjectCreatePage,
            },
            {
                path: '/compliance/projects/:id/edit',
                component: ComplianceProjectCreatePage,
            },
            /* Risk module */
            {
                path: '/risks/dashboard',
                component: Dashboard,
            },
            {
                path: '/risks/risks-register/create',
                component: RiskRegisterCreate,
            },
            {
                path: '/risks/risks-register/:id/edit',
                component: RiskRegisterCreate,
            },
            {
                path: '/risks/risks-register/:id/show',
                component: RiskRegisterShow,
            },
            {
                path: '/risks/risks-register',
                component: RiskRegisterPage,
            },
            /* Risk module end */
            /* policy-management module */
            {
                path: '/policy-management/campaigns',
                component: CampaignPage,
            },
            /* Risk-Setup module routes */
            {
                path: '/risks/setup',
                component: RiskSetup,
            },
            {
                path: '/risks/wizard/setup',
                component: RiskSetupWizard,
            },
            {
                path: '/risks/manual/setup-react',
                component: ManualRiskSetup,
            },
            /* Controls routes */
            {
                path: '/compliance/implemented-controls',
                component: Controls,
            },
            /* user-management module */
            {
                path: '/users/view',
                component: UserList,
            },
            {
                path: '/users/create',
                component: UserCreatePage,
            },
            {
                path: '/users/edit/:id',
                component: UserEditPage,
            },
            {
                path: '/asset-management',
                component: AssetManagememt,
            },
            {
                path: '/kpi-dashboard',
                component: KPIDashboard,
            },
            // Integrations
            {
                path: '/integrations',
                component: Integrations
            }
        ]
    }
]

export default routes;
