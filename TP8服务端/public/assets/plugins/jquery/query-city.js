/**
 * Created by Administrator on 2017/8/26.
 */
function cityChange(provinceQuery,cityQuery,districtQuery) {

    $(provinceQuery).change(function () {
        citys($(provinceQuery).val());
    });

    $(cityQuery).change(function () {
        districts($(cityQuery).val());
    });

    function provinces() {
        $.ajax({
            url : basePath+"web/City_allProvince",
            data : {

            },
            dataType :"JSON",
            cache : false,
            type : "POST",
            success : function(dat){
                if(dat.code==1){
                    console.log(dat.data);
                    var html ='';
                    $(dat.data).each(function (index,bean) {
                        html+='<option value="'+bean.id+'">'+bean.shortname+'</option>';
                    });
                    $(provinceQuery).html(html);
                    citys($(provinceQuery).val());
                }else if(dat.code==0){

                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    }


    function citys(provinceId) {
        $.ajax({
            url : basePath+"web/City_cityList",
            data : {
                provinceId:provinceId
            },
            dataType :"JSON",
            cache : false,
            type : "POST",
            success : function(dat){
                if(dat.code==1){
                    console.log(dat.data);
                    var html ='';
                    $(dat.data).each(function (index,bean) {
                        html+='<option value="'+bean.id+'">'+bean.shortCityName+'</option>';
                    });
                    $(cityQuery).html(html);
                    districts($(cityQuery).val());
                }else if(dat.code==0){

                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    }

    function districts(cityId) {
        $.ajax({
            url : basePath+"web/City_districtList",
            data : {
                cityId:cityId
            },
            dataType :"JSON",
            cache : false,
            type : "POST",
            success : function(dat){
                if(dat.code==1){
                    console.log(dat.data);
                    var html ='';
                    $(dat.data).each(function (index,bean) {
                        html+='<option value="'+bean.id+'">'+bean.districtName+'</option>';
                    });
                    $(districtQuery).html(html);
                }else if(dat.code==0){

                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    }
    provinces();
}