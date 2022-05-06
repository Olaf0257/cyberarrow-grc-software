import React, { useState, useEffect } from "react";

import { useForm, usePage } from "@inertiajs/inertia-react";

const SAMLSettingsRemoveButton = () => {
    const {
        form_actions: { saml_settings },
    } = usePage().props;

    const { processing, post } = useForm();

    const [isSSOConfigured, setIsSSOConfigured] = useState(false);
    const removeSAMLConfig = (e) => {
        e.preventDefault();
        AlertBox(
            {
                title: "Are you sure?",
                text: "All the SAML Configuration will be deleted!",
                showCancelButton: true,
                confirmButtonColor: "#ff0000",
                confirmButtonText: "Yes, delete it!",
                icon: 'warning',
                iconColor: '#ff0000'
            },
            function (confirmed) {
                if (confirmed.value) {
                    post(saml_settings.remove, { preserveState: false });
                }
            }
        )
    }

    function getSAMLConfiguration() {
        axiosFetch.get(route("getSAMLConfiguration")).then((response) => {
            if (response.status) {
                setIsSSOConfigured(response.data);
            }
        });
    }

    useEffect(() => {
        getSAMLConfiguration();
    }, []);

    return (
        <>
            {isSSOConfigured &&
                <button
                    onClick={(e) => removeSAMLConfig(e)}
                    className="btn btn-danger width-lg me-2"
                    disabled={processing}
                >
                    {processing ? "Removing" : "Remove SAML Config"}
                </button>
            }
        </>
    );
};

export default SAMLSettingsRemoveButton;
