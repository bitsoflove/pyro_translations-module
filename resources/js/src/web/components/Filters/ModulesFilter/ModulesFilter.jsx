import React from 'react';
import Select2 from 'react-select2-wrapper';
import _ from 'lodash';

class ModulesFilter extends React.Component  {

    getOptions() {
        const props = this.props;
        const modules = props.modules;

        var options = (modules && modules.length) ? modules.map(function(item) {
            return {
                id: item.id,
                text: item.name
            }
        }) : [];
        return options;
    }

    onChange(event) {
        event.stopPropagation();

        const value = $(event.target).find('option:selected').map(function(){return this.value;}).toArray();
        if(value !== this.props.selected) {
            this.props.onChange(value);
        }
    }

    render() {

        const options = this.getOptions();

        return (
            <div>
                <div className="form-group">
                    <label htmlFor="" className="control-label">Modules</label> <br />
                    <Select2
                        ref="select"
                        multiple
                        selected={this.props.selected}
                        value={this.props.selected}
                        data={options}
                        onChange={this.onChange.bind(this)}
                        options={
                        {
                          placeholder: 'Modules',
                          width: '100%',
                        }
                      }
                    />
                </div>
            </div>
        );
    }
}

export default ModulesFilter;
