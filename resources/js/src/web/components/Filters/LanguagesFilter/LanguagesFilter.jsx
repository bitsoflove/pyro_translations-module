import React from 'react';
import Select2 from 'react-select2-wrapper';

class LanguagesFilter extends React.Component  {

    getOptions() {
        const props = this.props;
        const languages = props.languages;
        var options = languages.map(function(locale) {
            if(locale !== props.without) {
                return {
                    id: locale,
                    text: locale
                }
            }
        });
        return options;
    }

    onChange(event) {
        event.stopPropagation();

        const value = $(event.target).find('option:selected').map(function(){return this.value;}).toArray();
        if(value !== this.props.selected) {
            console.log('onChange', value);
            this.props.onChange(value);
        }
    }

    render() {

        const options = this.getOptions();
        return (
            <div>
                <div className="form-group">
                    <label htmlFor="" className="control-label">Translate to</label> <br />
                    <Select2
                        ref="select"
                        multiple
                        selected={this.props.selected}
                        value={this.props.selected}
                        data={options}
                        onChange={this.onChange.bind(this)}
                        options={
                        {
                          placeholder: 'Translate to...',
                          width: '100%',
                        }
                      }
                    />
                </div>
            </div>
        );
    }
}

export default LanguagesFilter;
