import React from "react";

export class GameInviteOptionsHandler
{
    constructor(options)
    {
        this.options = options;
        this.elementPrefix = 'gameBoxOption-';

        this.optionKeyIcons = {
            numberOfPlayers: 'fa fa-users',
            autostart: 'fa fa-refresh',
            forfeitAfter: 'fa fa-hourglass',
        }
    }

    getRenderedOptions()
    {
        const renderedOptions = [];

        for (const [key, values] of Object.entries(this.options)) {

            if (this.#hasSingleValue(key)) {
                continue;
            }

            switch (values.type) {
                case 'radio':
                    renderedOptions.push(<div key={key}>{this.#getRenderedRadio(key)}</div>);
                    break;
                case 'checkbox':
                    renderedOptions.push(<div key={key}>{this.#getRenderedCheckbox(key)}</div>);
                    break;
                case 'select':
                    renderedOptions.push(<div key={key}>{this.#getRenderedRadio(key)}</div>);
                    break;
                default:
                    renderedOptions.push(<div key={key}>{this.#getRenderedRadio(key)}</div>);
                    break;
            }
        }

        return <>{renderedOptions}</>;
    }

    getOptionsValues()
    {
        const options = {};
        for (const [key, values] of Object.entries(this.options)) {

            if (this.#hasSingleValue(key)) {
                options[key] = this.#getOptionDefault(key);
                continue;
            }

            let selector = '';

            switch (values.type) {
                case 'radio':
                    selector = 'input[name="' + this.#getElementName(key) + '"]:checked';
                    options[key] = document.querySelector(selector).value;
                    break;

                case 'checkbox':
                    selector = 'input[name="' + this.#getElementName(key) + '"]';
                    options[key] = document.querySelector(selector).checked ? 1 : 0;
                    break;

                case 'select':
                    selector = 'input[name="' + this.#getElementName(key) + '"]:checked';
                    options[key] = document.querySelector(selector).value;
                    break;

                default:
                    selector = 'input[name="' + this.#getElementName(key) + '"]:checked';
                    options[key] = document.querySelector(selector).value;
                    break;
            }
        }

        return options;
    }

    #getRenderedRadio(key)
    {
        const radioOptions = this.options[key].availableValues.map((number) => {
            const id = key + '-' + number;
            return (
                <div className="mx-2 flex items-center" key={'div' + id}>
                    <input type="radio" id={id} name={this.#getElementName(key)} value={number} required defaultChecked={number === this.#getOptionDefault(key)} />
                    <label className="text-white font-medium font-semibold px-2 mb-0" htmlFor={id}>{number}</label>
                </div>
            );
        });

        return (
            <>
                {this.#getRenderedMeta(key)}

                <div className="flex items-center mb-4">
                    {radioOptions}
                </div>
            </>
        );
    }

    #getRenderedCheckbox(key)
    {
        const checkboxOption = (
            <div className="mx-2 flex items-center">
                <input type="checkbox" id={key} name={this.#getElementName(key)} value={1} defaultChecked={this.#getOptionDefault(key)}/>
                <label className="text-white font-medium font-semibold px-2 mb-0" htmlFor={key}>Enabled</label>
            </div>
        );

        return (
            <>
                {this.#getRenderedMeta(key)}

                <div className="flex items-center mb-4">
                    {checkboxOption}
                </div>
            </>
        );
    }

    #getRenderedMeta(key)
    {
        return (
            <>
                <div className="flex items-center mb-0">
                    <i className={this.#getOptionIcon(key) + " text-white mr-2 content-center"}></i>
                    <h5 className="text-white mr-2">{this.#getOptionName(key)}</h5>
                </div>

                <p className="mb-1">{this.#getOptionDescription(key)}</p>
            </>
        );
    }

    #hasSingleValue(key)
    {
        return this.options[key].availableValues.length === 1;
    }

    #getOptionDefault(key)
    {
        return this.options[key].defaultValue;
    }

    #getElementName(key)
    {
        return this.elementPrefix + key;
    }

    #getOptionIcon(key)
    {
        return this.optionKeyIcons[key] ?? '';
    }

    #getOptionName(key)
    {
        return this.options[key].name;
    }

    #getOptionDescription(key)
    {
        return this.options[key].description;
    }
}
