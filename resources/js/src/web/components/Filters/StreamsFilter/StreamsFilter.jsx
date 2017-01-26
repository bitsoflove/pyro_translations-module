import React from 'react';
import Select2 from 'react-select2-wrapper';
import _ from 'lodash';

class StreamsFilter extends React.Component  {

    getOptions() {
        const props = this.props;
        const streams = props.streams;
        var options = streams.map(function(item) {
            return {
                id: item.id,
                text: item.slug
            }
        });
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
                    <label htmlFor="" className="control-label">Streams</label> <br />
                    <Select2
                        ref="select"
                        multiple
                        selected={this.props.selected}
                        value={this.props.selected}
                        data={options}
                        onChange={this.onChange.bind(this)}
                        options={
                        {
                          placeholder: 'Streams',
                          width: '100%',
                        }
                      }
                    />
                </div>
            </div>
        );
    }
}

export default StreamsFilter;
