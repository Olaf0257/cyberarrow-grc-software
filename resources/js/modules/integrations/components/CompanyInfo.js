import {Link} from "@inertiajs/inertia-react";

function CompanyInfo(props) {
    const {name,logo,description,comingSoon}=props;

    return (
        <div className="card bg-pattern h-100">
            <div className="card-body">
                <div className="badge bg-soft-info text-info float-end coming-soon-badge">Coming soon</div>
                <div className="clearfix"/>
                <div className="text-center">
                <img src={logo} alt="" className="avatar-xl mb-3" />
                <h4 className="mb-1 font-20">{name}</h4>
            </div>

                <p className="font-14 text-center text-muted">{description}</p>

                <div className="text-center">
                    <Link
                        className="btn btn-link disabled btn-sm width-sm"
                        href="#"
                    >
                        Connect
                    </Link>
                </div>
            </div>
        </div>
    )
}

export default CompanyInfo;