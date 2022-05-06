import AuthLayout from "../../../layouts/auth-layout/AuthLayout";
import Logo from "../../../layouts/auth-layout/components/Logo";

const CampaignPolicyAcknowledgement = (props) => {
    return (
        <AuthLayout>
            <div className="card">
                <div className="card-body bg-pattern">
                    {/* LOGO DISPLAY NAME */}
                    <Logo />
                    <div className="card-body">{props.children}</div>
                </div>{" "}
            </div>
        </AuthLayout>
    );
};

export default CampaignPolicyAcknowledgement;
