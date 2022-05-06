import React, { Fragment } from 'react';
import { useHistory } from 'react-router-dom';
import { Link } from "@inertiajs/inertia-react";
import './style.css'
import './style.scss'

export default function ErrorPage({ status, prev_url }) {
  if(prev_url === window.location.href){
    prev_url=appBaseURL;
  }
  const title = {
    503: '503: Service Unavailable',
    500: '500: Server Error',
    404: '404: Page Not Found',
    403: '403: Forbidden',
  }[status]

  const description = {
    503: 'Sorry, we are doing some maintenance. Please check back soon.',
    500: 'Server Error.',
    404: 'Sorry, the page you are looking for could not be found.',
    403: 'Sorry, you are forbidden from accessing this page.',
  }[status]

  return (
    <Fragment>
      {status===403 ?
          <div id="error_page">
              <div className="maincontainer">
                    <title>{title}</title>
                  <div className="bat">
                    <img className="wing leftwing"
                        src={`${appBaseURL}/assets/images/error/bat-wing.png`} />
                    <img className="body"
                        src={`${appBaseURL}/assets/images/error/bat-body.png`} alt="bat" />
                    <img className="wing rightwing"
                        src={`${appBaseURL}/assets/images/error/bat-wing.png`} />
                  </div>
                  <div className="bat">
                    <img className="wing leftwing"
                        src={`${appBaseURL}/assets/images/error/bat-wing.png`} />
                    <img className="body"
                        src={`${appBaseURL}/assets/images/error/bat-body.png`} alt="bat" />
                    <img className="wing rightwing"
                        src={`${appBaseURL}/assets/images/error/bat-wing.png`} />
                  </div>
                  <div className="bat">
                    <img className="wing leftwing"
                        src={`${appBaseURL}/assets/images/error/bat-wing.png`} />
                    <img className="body"
                        src={`${appBaseURL}/assets/images/error/bat-body.png`} alt="bat" />
                    <img className="wing rightwing"
                        src={`${appBaseURL}/assets/images/error/bat-wing.png`} />
                  </div>
                  <img className="foregroundimg" src={`${appBaseURL}/assets/images/error/HauntedHouseForeground.png`} alt="haunted house" />

                </div>
              <h1 className="errorcode">{title}</h1>
              <div className="errortext">{description}
                <Link href={prev_url} style={{color:'black'}}> Go Back</Link>
              </div>
          </div>
          :
      <div className="error_page_2 relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
            <div className="max-w-xl mx-auto sm:px-6 lg:px-8">
                <div className="flex items-center pt-8 sm:justify-start sm:pt-0">
                    <div className="px-4 text-lg text-gray-500 border-r border-gray-400 tracking-wider">
                        {status} |  {description} <Link href={prev_url} style={{color:'black'}}> Go Back</Link>
                    </div>
                </div>
            </div>
        </div>
      }
    </Fragment>
  )
}