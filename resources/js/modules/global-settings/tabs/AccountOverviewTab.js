import React ,{ useState }  from 'react';
import {Table} from "react-bootstrap";
import LoadingButton from '../../../common/loading-button/LoadingButton';
import { usePage } from "@inertiajs/inertia-react";

const AccountOverviewTab = (props) => {
    const {license}=usePage().props;
    const [processing, setProcessing] = useState(false);
    const [updateAvailableDetails, setUpdateAvailableDetails] = useState(false);
    const [updateMessage, setUpdateMessage] = useState('');
    const [downloadUpdateMessage, setDownloadUpdateMessage] = useState('');
    const [downloadCompleted, setDownloadCompleted] = useState(false);

    const handleCheckUpdate = async() =>{
        setUpdateMessage('');
        setProcessing(true);
        setUpdateAvailableDetails(false);
        const {data} = await axiosFetch.get(route('license.check.update'));
        setProcessing(false);
        setUpdateMessage(data.message);
        if(data.status)
            setUpdateAvailableDetails(data);
    }

    const handleUpdate = async() =>{
        setProcessing(true);
        setDownloadUpdateMessage('');
        const {data} = await axiosFetch.post(route('license.download.update'));
        setDownloadUpdateMessage(data);
        setProcessing(false);
        setDownloadCompleted(true);
    }

    return (
        <Table hover>
            <tbody>
                <tr>
                    <td>Name</td>
                    <td>{license.licensedTo}</td>
                </tr>
                <tr>
                    <td>License Expiration</td>
                    <td>{license.licenseExpiryDate}</td>
                </tr>
                <tr>
                    <td>Version</td>
                    <td>{license.currentVersion}</td>
                </tr>
                <tr>
                    <td>
                    {updateMessage=="" &&
                        <LoadingButton
                            className="btn btn-primary"
                            type="submit"
                            onClick={handleCheckUpdate}
                            loading={processing}
                            disabled={processing}
                        >
                            Check for updates
                        </LoadingButton>
                    }
                    </td>
                    <td>
                        {updateMessage}
                        <br />
                        {updateAvailableDetails && !downloadCompleted &&
                        <span>
                            <p className="font-bold"><b>Version: {updateAvailableDetails.version}</b></p>
                            <p className="font-bold"><b>Released Date: {updateAvailableDetails.release_date}</b></p>
                            <p>{updateAvailableDetails.summary}</p>
                                <LoadingButton
                                    className="btn btn-primary"
                                    type="submit"
                                    onClick={handleUpdate}
                                    loading={processing}
                                    disabled={processing}
                                >
                                    Download and install 
                                </LoadingButton>
                            {processing &&
                                <p>Please do not refresh the page.</p>
                            }
                        </span>
                        }
                        {
                            downloadCompleted && 
                            <span>
                                <div
                                dangerouslySetInnerHTML={{
                                    __html: downloadUpdateMessage,
                                }}
                                ></div>
                                 <LoadingButton
                                    className="btn btn-primary"
                                    type="submit"
                                    onClick={()=>{window.location.reload()}}
                                    loading={processing}
                                    disabled={processing}
                                >
                                    Refresh
                                </LoadingButton>
                            </span>
                        }
                    </td>
                </tr>
            </tbody>
        </Table>
    )
};

export default AccountOverviewTab;

