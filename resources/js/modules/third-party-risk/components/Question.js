import React from "react";
import {ButtonGroup, ToggleButton} from "react-bootstrap";

const Question = ({answer, setAnswer, question, id}) => {
    const radios = [
        {name: "Yes", value: "Yes",variant:'success'},
        {name: "No", value: "No",variant:'danger'},
        {name: "Partial", value: "Partial",variant:'warning'},
        {name: "Not Applicable", value: "Not Applicable",variant:'blue'},
    ];


    return (
        <>
            <div className="row my-2">
                <div className="col-lg-6 col-xl-8 mb-2 mb-lg-0">{question}</div>
                <div className="col-lg-6 col-xl-4">
                    <ButtonGroup>
                        {radios.map((radio, index) => (
                            <ToggleButton
                                key={`radio-${id}-${index}`}
                                id={`radio-${id}-${index}`}
                                type="radio"
                                variant={radio.variant}
                                name={"radio"+id}
                                value={radio.value}
                                checked={answer === radio.value}
                                onChange={(e) =>
                                    setAnswer(e.currentTarget.value)
                                }
                            >
                                {radio.name}
                            </ToggleButton>
                        ))}
                    </ButtonGroup>
                </div>
            </div>
        </>
    );
}

export default React.memo(Question, (prevProps, nextProps) => {
    return prevProps.answer === nextProps.answer;
});
