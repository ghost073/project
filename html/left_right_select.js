/**
* 左右select移动
*
* @version v1.0 2011/11/30 22:24
* @author wb_develop@sina.cn
*
*/

function removeAll(oSelect){   
    for (i = oSelect.options.length -1;i>=0;i--){   
         var node = oSelect.options[i];   
         oSelect.removeChild(node);   
    }   
 }   

 // 全部移动
function insertAllLR(sid, tid)
{
    var t_obj = document.getElementById(tid);
    var s_obj = document.getElementById(sid);
    var s_obj_options_len = s_obj.length;
    for (var i = 0;i < s_obj_options_len; i++)   
    {   
         var e = s_obj.options[i];   
         insert(t_obj, e);   
    }   
    removeAll(s_obj);   
}
 
 // 左右选中移动
function insertLR(sid, tid)
{
    var t_obj = document.getElementById(tid);
    var s_obj = document.getElementById(sid);
    var s_obj_options_len = s_obj.length;
    for (var i = 0; i < s_obj_options_len; i++)
    {
        var e = s_obj.options[i];
        if (e.selected)
        {
            insert(t_obj, e);
        }
    }
    removeSelected(s_obj);
} 

function insert(oDest,e){   
         var oNewNode = document.createElement("option");   
         oNewNode.innerText = e.innerText;   
         oNewNode.value = e.value;   
         oNewNode.text = e.text;
         oNewNode.lable = e.lable;
         addUniqueNode(oNewNode, oDest);  
 }   

function  addUniqueNode(node, oDest){   
    var oNewNode = document.createElement("option");   
    var nodeExist = false;   
    for(var y in oDest.options)
    {   
        if(node.value == oDest.options[y].value){   
            nodeExist = true;   
            break;   
        }   
   }   

    if(!nodeExist){   
       var newNode = node.cloneNode(true);   
       oDest.appendChild(newNode);   
    }       
}   

function removeSelected(oSelect){   
    for( i=oSelect.options.length -1;i>=0;i--){   
         var node = oSelect.options[i];   
         if(node.selected){   
            oSelect.removeChild(node);   
         }   
    }   
}

/**
 * 右侧全选
 *
 * @param string id 选择的ID
 */
function rightAllSelect(id) {
    var rightObj = document.getElementById(id);
    var rightLen = rightObj.length;
    for (var i = 0; i < rightLen; i ++) {
        rightObj.options[i].selected = 'selected';
    }
}