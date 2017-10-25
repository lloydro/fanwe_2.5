
    var index = 0;
    var cosBox = [];

    var getSignature = function(callback){
        $.ajax({
            url: APP_ROOT + 'm.php?m=VideoPlayback&a=new_sign',
            type: 'POST',
            dataType: 'json',
            success: function(res){
                if(res.status ==1 && res.signature) {
                    callback(res.signature);
                } else {
                    $.showErr('获取签名失败');
                }

            }
        });
    };
    $("#video_file").on('change',function(e){
        var num = addUploaderMsgBox('hasVideo');
        var videoFile = this.files[0];
        $('#result').append(videoFile.name +　'\n');
        var resultMsg = qcVideo.ugcUploader.start({
            videoFile: videoFile,
            getSignature: getSignature,
            allowAudio: 1,
            success: function(result){
                if(result.type == 'video') {
                    $('[name=videoresult'+num+']').text('上传成功');
                    $('[name=cancel'+num+']').remove();
                    $('[name=del'+num+']').css('display','inline-block');
                    cosBox[num] = null;
                } else if (result.type == 'cover') {
                    $('[name=coverresult'+num+']').text('上传成功');
                }

                console.log('上传成功的文件类型：' + result.type);
            },
            error: function(result){
                var msg = 'message:' + result.msg;
                $('#error').html(msg);
                console.log('上传失败的文件类型：' + result.type);
                console.log('上传失败的原因：' + result.msg);
            },
            progress: function(result){
                if(result.type == 'video') {
                    $('[name=videoname'+num+']').text(result.name);
                    $('[name=videosha'+num+']').text(Math.floor(result.shacurr*100)+'%');
                    $('[name=videocurr'+num+']').text(Math.floor(result.curr*100)+'%');
                    $('[name=cancel'+num+']').attr('taskId', result.taskId);
                    cosBox[num] = result.cos;
                } else if (result.type == 'cover') {
                    $('[name=covername'+num+']').text(result.name);
                    $('[name=coversha'+num+']').text(Math.floor(result.shacurr*100)+'%');
                    $('[name=covercurr'+num+']').text(Math.floor(result.curr*100)+'%');
                }

                console.log('上传进度的文件类型：' + result.type);
                console.log('上传进度的文件名称：' + result.name);
                console.log('上传进度：' + result.curr);
            },
            finish: function(result){
                $('[name=videofileId'+num+']').text(result.fileId);
                $('[name=videourl'+num+']').text(result.videoUrl);
                if(result.message) {
                    $('[name=videofileId'+num+']').text(result.message);
                }
                $("#file_id").val(result.fileId);
                console.log('上传结果的fileId：' + result.fileId);
                console.log('上传结果的视频名称：' + result.videoName);
                console.log('上传结果的视频地址：' + result.videoUrl);
            }
        });
        if(resultMsg){
            $('#video_file').val("");
        }
    });

    $("#btn_upload").on('click',function(){
        $('#video_file').click();
    });
    /*
     * 取消上传绑定事件，示例一与示例二通用
     */
    $('#result').on('click', '[act=cancel-upload]', function() {
        var cancelresult = qcVideo.ugcUploader.cancel({
            cos: cosBox[$(this).attr('cosnum')],
            taskId: $(this).attr('taskId')
        });
        $(".uploaderMsgBox").remove();
        console.log(cancelresult);
    });
    $('#result').on('click','[data-act="del"]', function() {
        $("#file_id").val('');
        $(".uploaderMsgBox").remove();
        $('#result').text('');
    });
    /**
     * 添加上传信息模块
     */

    var addUploaderMsgBox = function(type){
        var html = '<div class="uploaderMsgBox" name="box'+index+'">';
        if(!type || type == 'hasVideo') {
            html += '视频名称：<span name="videoname'+index+'"></span>；' +
                '计算sha进度：<span name="videosha'+index+'">0%</span>；' +
                '上传进度：<span name="videocurr'+index+'">0%</span>；' +
                'fileId：<span name="videofileId'+index+'">   </span>；' +
                '上传结果：<span name="videoresult'+index+'">   </span>；<br>' +
                '地址：<span name="videourl'+index+'">   </span>；'+
                '<span data-act="del" style="display: none" name="del'+index+'" class="delete">删除</span>；'+
                '<a href="javascript:void(0);" name="cancel'+index+'" cosnum='+index+' act="cancel-upload">取消上传</a><br>';
        }

        if(!type || type == 'hasCover') {
            html += '封面名称：<span name="covername'+index+'"></span>；' +
                '计算sha进度：<span name="coversha'+index+'">0%</span>；' +
                '上传进度：<span name="covercurr'+index+'">0%</span>；' +
                '上传结果：<span name="coverresult'+index+'">   </span>；<br>' +
                '地址：<span name="coverurl'+index+'">   </span>；<br>' +
                '</div>'
        }
        html += '</div>';

        $('#result').append(html);
        return index++;
    };
