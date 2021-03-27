
//////////////////////////////////////////////////////////

var EditView = React.createClass({
    contextTypes: {
        status: React.PropTypes.any,
        fields: React.PropTypes.any,
    },
    render: function() {

        var fields = this.context.fields;

        var convertTD = function(item) {

            var text = item.text;
            var type = item.type;
            item.id = item.id||item.name;
            item.name = item.name||item.id;
            item.class = item.class||"form-control";

            if(text && type==undefined)
            {
                return <td>{text}</td>;
            }

            var html="";
            var attr = {};
            attr['id'] = item.id;
            attr['name'] = item.name;
            attr['className'] = item.class;
            attr['type'] = item.type;
            attr['defaultValue'] = item.value;
            for(var i in item.attr)
                for(var key in item.attr[i])
                    attr[key] = item.attr[i].key

            if(type == "text")
            {
                html = <input {...attr} />
            }
            else if(type=="submit")
            {
                var btn_name="新增";
                if(this.context.status=="update"){btn_name="更新"}
                attr['value'] = btn_name;

                html = <input {...attr} />
                //{變數+" "+變數}一樣可做變數相加
            }
            else if(type=="checkbox")
            {
                attr['value'] = item.value;
                if(item.checked){
                    attr['defaultChecked'] = true;
                }
                html = <input {...attr} />
            }
            else if(type=="select")
            {
                html = <MSelect attr={attr} option={item.option}/>
            }

            return <td colSpan={item.colspan}>{html}</td>;

        }.bind(this)

        var convertTR = function(item) {
            return <tr>{item.map(convertTD)}</tr>
        };

        return (<table  className="table table-v table-condensed">{fields.map(convertTR)}</table>);
    }
});

//////////////////////////////////////////////////////////////////////////////
var MSelect = React.createClass({

    render: function() {

        var attr = this.props.attr
        var convertOption = function(item){
            return <option value={item.value}>{item.name}</option>
        }

        return <select {...attr} onChange={this.props.onchange} >{this.props.option.map(convertOption)}</select>
    }
})

//////////////////////////////////////////////////////////////////////////////

var ListView = React.createClass({

    contextTypes: {
        chgStatus: React.PropTypes.any
    },
    handleClick: function(_status,_pkeys) {
        this.context.chgStatus(_status,_pkeys)
    },
    componentWillMount: function () {
        this.setState({
            data_list:data_list
        })
    },
    userChg:function(e){
        this.setState({
            data_list:
                data_list.filter(function(x,i){
                    return i==0 || x.UserID==e.target.value || e.target.value=="" || e.target.value=="root";
                })
        })
        //console.log(data_list.filter(function(x,i){
        //    return i==0 || x.UserID==e.target.value;
        //}));
    },
    render: function(){

        var index = 0;


        var convertTR = function(item,_index) {

            index = _index;

            if(index>0)
            {
                var pkeys = {};
                for(var i in key_field)
                {
                    pkeys[key_field[i]] = item[key_field[i]];
                }

                return (
                    <tr data-update>
                        {Object.keys(item).map(function(value) {
                            var attr = {};
                            attr['data-bind'] = value
                            for(var i in key_field)
                                if(value==key_field[i])
                                    attr['data-index'] = true

                            return <td onClick={this.handleClick.bind(this,'update')} {...attr}>{item[value]}</td>
                            }.bind(this))}
                        <td><input type='button' className="btn btn-danger delete_btn" value="Delete" value='刪除' onClick={this.handleClick.bind(this,'delete',pkeys)} /></td>
                    </tr>
                )
            }else{
                return <tr>{item.map(function(_item){return <td>{_item}</td>})}</tr>
            }

        }.bind(this);

        var userChg = typeof empSelect2!="undefined"?
            <div>用戶切換：<MSelect attr={empSelect2.attr} option={empSelect2.option} onchange={this.userChg}/></div>:
            "";

        return (
        <div>
            {userChg}
            <table className="table table-h table-striped table-hover table-pointer">
                <tbody>{this.state.data_list.map(convertTR)}</tbody>
            </table>
        </div>
        );
    }
})

////////////////////////////////////////////////////////////////////////

var View = React.createClass({
    getInitialState: function() {
        return {
            status: "add",
            pkeys:{},
        };
    },
    componentWillMount: function () {
        this.setState({
            fields:this.props.edit_field
        })
    },
    childContextTypes: {
        fields: React.PropTypes.any,
        status: React.PropTypes.any,
        chgStatus: React.PropTypes.any,
    },
    getChildContext: function(){
        return {
            fields: this.state.fields,
            status: this.state.status,
            chgStatus: this.chgStatus,
        }
    },
    chgStatus: function (_status,_pkeys) {
        this.setState({
            status:_status,
            pkeys:_pkeys,
        });
    },
    render: function() {
        var del_btn
        if (this.props.delete_all!="false")
            del_btn = <input type="button" className="btn btn-danger delete_all_btn" value="Delete All" onClick={this.chgStatus.bind(this,'deleteAll')}/>
        return (
            <form method="post">
                <input type="hidden" name="status" value={this.state.status}/>
                <input type="hidden" name="submit" value="1"/>
                {Object.keys(this.state.pkeys).map(function(pkey){
                    return <input type="hidden" name={pkey} value={this.state.pkeys[pkey]}/>
                }.bind(this))}
                <EditView />
                {del_btn}
                <ListView/>
            </form>
        );
    }
});

