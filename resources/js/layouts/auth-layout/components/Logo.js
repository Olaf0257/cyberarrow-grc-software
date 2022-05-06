import React, { Fragment } from 'react';
import { Link, usePage } from '@inertiajs/inertia-react';
import FlashMessages from '../../../common/FlashMessages'

const Logo = () => {
    const { globalSetting, APP_URL , file_driver } = usePage().props;
    return (
        <Fragment>
            <div className="text-center w-75 m-auto">
                <Link href={route('login')}>
                    {file_driver =="s3"?
                        <span><img className="logo-sm" src={globalSetting.company_logo==="assets/images/ebdaa-Logo.png"? APP_URL + globalSetting.company_logo: globalSetting.company_logo } alt="Company logo" height="" width="140" /></span>
                        :
                        <span><img className="logo-sm" src={globalSetting.company_logo==="assets/images/ebdaa-Logo.png"? APP_URL + globalSetting.company_logo: asset(globalSetting.company_logo) } alt="Company logo" height="" width="140" /></span>
                    }
                </Link>
                <p className="log-text text-muted mb-3 mt-3">{decodeHTMLEntity(globalSetting.display_name)}</p>
            </div>
            <FlashMessages></FlashMessages>
        </Fragment>
    )
}

export default Logo

