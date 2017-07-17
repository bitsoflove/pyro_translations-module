import React from 'react';
import HotTable from 'react-handsontable';

class Sheet extends React.Component  {

    constructor(props) {
        super(props);

        this.changes = {};
        console.warn('wrs beter om changes mee te geven via props, dan moet dit niet ontdubbeld worden');
    }

    cells(columnCount, row, col, prop) {
        var cellProperties = {};

        var isChanged = !!this.changes["" + row + '-' + col];
        if(isChanged) {
            cellProperties.isChanged = true;
        }

        // set first two columns disabled
        if((col < 2)) {
            cellProperties.readOnly = true;
            cellProperties.className = 'read-only';
            cellProperties.renderer = this.renderDisabledCell;
        } else if (col === columnCount-1) {
            cellProperties.readOnly = true;
            cellProperties.className = 'read-only';

            cellProperties.renderer = this.renderActionsCell.bind(this);
        } else {

            cellProperties.renderer = this.renderTranslationCell;
        }

        return cellProperties;
    }

    createEntryLink(cellProperties) {
        var a = document.createElement('a');
        var linkText = document.createTextNode("View entry");
        a.appendChild(linkText);
        a.className = "btn btn-default btn-xs";
        a.title = 'View Entry';
        a.href = "#";

        a.addEventListener('click', function(){
            alert('todo');
        })

        return a;
    }

    createAutoTranslateLink(cellProperties) {
        var a = document.createElement('a');
        var linkText = document.createTextNode("Google Translate");
        a.appendChild(linkText);
        a.className = "btn btn-default btn-xs";
        a.title = 'Google Translate';
        a.href = "#";

        a.addEventListener('click', function(){
            alert('todo');
        })

        return a;
    }

    renderActionsCell(instance, td, row, col, prop, value, cellProperties) {

        var div = document.createElement('div');
        //div.className = classnames(styles.showHover);

        var span = document.createElement('span');
        span.className = 'pull-right ';

        var entryLink = this.createEntryLink(cellProperties);
        var autoTranslateLink = this.createAutoTranslateLink(cellProperties);

        Handsontable.Dom.empty(td);

        console.log("For now, disable 'view entry' and 'auto translate' functionality");

        //span.appendChild(entryLink);
        //span.appendChild(autoTranslateLink);
        div.appendChild(span);
        td.appendChild(div);

        return td;
    }

    renderTranslationCell(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.renderers.TextRenderer.apply(this, arguments);
        td.style['minWidth'] = '100px';

        if(cellProperties.isChanged) {
            td.style['backgroundColor'] = '#61259e';
            td.style['color'] = 'white';
        }

    }

    renderDisabledCell(instance, td, row, col, prop, value, cellProperties) {
        Handsontable.renderers.TextRenderer.apply(this, arguments);
        td.style.background = '#f3f3f3';
        td.style.color = '#black';
    }

    beforeChange(changes, source) {
        if(!changes) {
            return;
        }

        for(var i = 0; i < changes.length; i ++) {
            var change = changes[i];
            var row = change[0];
            var col = change[1];
            this.changes["" + row + "-" + col] = changes;
        }
    }

    afterChange(changes, source) {
        this.props.afterChange(changes, source);
    }

    componentWillReceiveProps(nextProps) {
        if(JSON.stringify(nextProps.changes) === "{}") {
            this.changes = {};
        }
    }

    shouldComponentUpdate(nextProps, nextState) {
        var sameData = (JSON.stringify(this.props.data) === JSON.stringify(nextProps.data));
        return !sameData;
    }


    render() {
        let data = this.props.data;
        let colHeaders = this.props.colHeaders;
        let columnCount = colHeaders.length;

        return (
            <div className="table-responsive" style={{minHeight: "800px"}}>
                <HotTable root="hot"
                          contextMenu={false}
                          rowHeaders={true}
                          colHeaders={colHeaders}
                          stretchH="last"
                          data={data}
                          manualColumnResize={true}
                          autoColumnSize={true}

                          maxRows={data.length}

                          cells={this.cells.bind(this, columnCount)}
                          beforeChange={this.beforeChange.bind(this)}
                          afterChange={this.afterChange.bind(this)}
                />

            </div>
        );
    }
}

export default Sheet;
