import React, {useState} from 'react';
import {ChromePicker} from "react-color";

const CustomColorPicker = ({color = '#fff', onChange}) => {
    const [displayColorPicker, setDisplayColorPicker] = useState(false);
    const [selectedColor, setSelectedColor] = useState(color);


    const handleColorChange = ({hex}) => {
        setSelectedColor(hex);
        onChange(hex);
    }

    const styles = {
            swatch: {
                padding: '5px',
                background: '#fff',
                borderRadius: '1px',
                boxShadow: '0 0 0 1px rgba(0,0,0,.1)',
                display: 'inline-block',
                cursor: 'pointer',
            },
            popover: {
                position: 'absolute',
                zIndex: '2',
            },
            cover: {
                position: 'fixed',
                top: '0px',
                right: '0px',
                bottom: '0px',
                left: '0px',
            },
        };

    return (
        <div>
            <div style={ styles.swatch } onClick={() => setDisplayColorPicker(!displayColorPicker)}>
                <div className="d-block" style={{backgroundColor: selectedColor, width: '20px', height: '20px'}} />
            </div>
            { displayColorPicker ? <div style={ styles.popover }>
                <div style={ styles.cover } onClick={ () => setDisplayColorPicker(false) }/>
                <ChromePicker color={ selectedColor } onChange={handleColorChange} disableAlpha />
            </div> : null }

        </div>
    );
};

export default CustomColorPicker;
