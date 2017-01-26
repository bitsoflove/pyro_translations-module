import React from 'react';
import Select2 from 'react-select2-wrapper';

class BaseLanguageFilter extends React.Component  {

    getOptions() {
        const languages = this.props.languages;
        var options = languages.map(function(locale) {
            return {
                id: locale,
                text: locale
            }
        });
        return options;
    }

    onChange(event) {
        event.stopPropagation();
        const value = event.target.value;
        if(value) {
            if(value !== this.props.selected) {
                console.log('onChange', value);
                this.props.onChange(value);
            }
        }
    }

    render() {

        const options = this.getOptions();

        return (
            <div>
                <div className="form-group">
                    <label htmlFor="" className="control-label">Translate from</label> <br />
                    <Select2
                        selected={this.props.selected}
                        value={this.props.selected}
                        data={options}
                        onChange={this.onChange.bind(this)}
                        options={
                        {
                          placeholder: 'Translate from...',
                          width: '100%',
                          height: '39px'
                        }
                      }
                    />
                </div>
            </div>
        );
    }
}

export default BaseLanguageFilter;
