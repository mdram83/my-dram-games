import React from 'react';

export const SiteButton = ({
    value,
    onClick = undefined,
    className = undefined,
    faClassName = undefined,
    disabled = false
}) => {

    const fontAwesomeIcon = faClassName ? <i style={{ letterSpacing: '0em'}} className={'fa ' + faClassName}></i>  : '';

    return (
        <button onClick={onClick ?? undefined} className={'site-btn ' + (className ?? '')} disabled={disabled}>
            {value} {fontAwesomeIcon}
        </button>
    );
}
