/*!
 * jQuery Filer
 * Copyright (c) 2013 CreativeDream
 * Website: http://creativedream.net/jquery.filer
 * Version: 1.0 (15-Nov-2013)
 * Requires: jQuery v1.7.1 or later 
*/
(function (e) {
    e.fn.filer = function (t) {
        var n = e.extend({}, e.fn.filer.defaults, t);
        this.each(function (t, r) {
            var i, s = e(r),
                o, u = e("input:file").length == 1 ? "" : "-" + e("input:file").index(s),
                a, f = {
                    init: function () {
                        i = this;
                        i._changeInput();
                        s.change(i.onChange);
                        if (n.dragDrop && !e.isEmptyObject(n.dragDrop)) {
                            i.dropZone = !n.dragDrop.dropBox ? o ? o : s : e(n.dragDrop.dropBox);
                            i.dropZone.bind("drop", i._drop).bind("dragenter", i._dragEnter).bind("dragover", i._dragOver).bind("dragleave", i._dragLeave)
                        }
                    },
                    _clickFile: function (e) {
                        if (o.find("#input-thumbs ul li").size() == 0 && e.target != o) {
                            s.click()
                        }
                    },
                    _changeInput: function () {
                        s.wrap('<div id="filer' + u + '"></div>');
                        a = s.parent("#filer" + u);
                        if (n.changeInput) {
                            switch (typeof n.changeInput) {
                            case "boolean":
                                caption = n.inputText;
                                o = e('<div class="file"><span class="filer-button">' + caption.choose + '</span><span class="filer-feedback">' + caption.feedback + "</span></div>");
                                s.after(o);
                                break;
                            case "string":
                                o = e(n.changeInput);
                                s.after(o);
                                break
                            }
                            s.css({
                                position: "absolute",
                                left: "-9999px",
                                top: "-9999px",
                                "z-index": "-9999"
                            });
                            o.on("click", i._clickFile)
                        }
                        if (n.limit >= 2) {
                            s.attr("multiple", "multiple");
                            s.attr("name").slice(-2) != "[]" ? s.attr("name", s.attr("name") + "[]") : null
                        }
                    },
                    _clearInput: function (t) {
                        if (!t) {
                            s.val("");
                            n.onEmpty != null && typeof n.onEmpty == "function" ? n.onEmpty(a, i.appendTo ? i.appendTo : "undefined") : null
                        }
                        if (n.changeInput) {
                            if (typeof n.changeInput == "boolean") {
                                a.find(o).html('<span class="filer-button">' + caption.choose + '</span><span class="filer-feedback">' + caption.feedback + "</span>")
                            } else {
                                a.find(o).html(e(n.changeInput).html())
                            }
                        }
                        a.find("div#input-thumbs").remove();
                        a.find('input[name^="inputOrdBox"]').remove();
                        i._setFeedbackText(0)
                    },
                    _setFeedbackText: function (e) {
                        var t = e == null ? i.files.length : e,
                            r = t == 0 ? n.inputText.feedback3 : t + " " + n.inputText.feedback2;
                        a.find(".file .filer-feedback").text(r)
                    },
                    _inputFilesCheck: function () {
                        var r = i.files;
                        if (typeof r == "undefined") {
                            return true
                        }
                        if (n.limit != null && r.length > n.limit) {
                            alert("Only " + n.limit + " files are allowed to be Upload!");
                            return false
                        }
                        for (t = 0; t < r.length; t++) {
                            var s = r[t].name.split(".").pop().toLowerCase();
                            if (n.extensions != null && e.inArray(s, n.extensions) == -1 && (n.newExt == null || e.inArray(s, n.newExt) == -1)) {
                                alert("Only " + n.types + " files are allowed!");
                                return false;
                                break
                            }
                            if (n.maxSize != null && r[t].size > n.maxSize * 1048576) {
                                alert(r[t].name + " is too large! Please upload file up to " + n.maxSize + "MB!");
                                return false;
                                break
                            }
                        }
                        return true
                    },
                    _thumbBox: function (t, r) {
                        var s = i.files,
                            o = n.maxChar,
                            u = n.iconPath.slice(-1) != "/" ? n.iconPath + "/" : n.iconPath,
                            a = i.appendTo.find("div#input-thumbs ul");
                        var f = s[t].name.split(".").pop().toLowerCase(),
                            l = s[t].name.slice(0, -f.length).length > o ? "..." : ".",
                            c = s[t].name.slice(0, -f.length - 1).slice(0, o - 2) + l + f,
                            h = s[t].name,
                            p = (s[t].size / 1048576).toString().slice(0, 5),
                            d = s[t].type.split("/", 1).toString().toLowerCase(),
                            v = n.removeFiles ? ' data-idx="' + t + '"' : "",
                            m = n.removeFiles ? "<span></span>" : "",
                            g = "<li%remove-index%>" + (n.template != null ? n.template : '<img src="%image-url%" title="%original-name%" /><em>%title%</em>%remove-icon%') + "</li>";
                        switch (d) {
                        case "image":
                            if (window.File && window.FileList && window.FileReader) {
                                var y = new FileReader;
                                y.onload = function (n) {
                                    var o = '<img src="' + n.target.result + '" title="' + s[t].name + '" />';
                                    e(o).error(function () {
                                        alert(s[t].name + " is broken Image!");
                                        console.log("What are you trying to do with „" + s[t].name + "”?");
                                        i._clearInput()
                                    }).load(function () {
                                        g = i._tempChange(g, c, h, d, p, f, n.target.result, t, m, v);
                                        e(g).hide().prependTo(a).fadeIn("slow", function () {
                                            i._onSelect(t, r)
                                        })
                                    })
                                };
                                y.readAsDataURL(s[t])
                            } else {
                                g = i._tempChange(g, c, h, d, p, f, u + "image.png", t, m, v);
                                e(g).hide().prependTo(a).fadeIn("slow", function () {
                                    i._onSelect(t, r)
                                })
                            }
                            break;
                        case "audio":
                            g = i._tempChange(g, c, h, d, p, f, u + "audio.png", t, m, v);
                            e(g).hide().prependTo(a).fadeIn("slow", function () {
                                i._onSelect(t, r)
                            });
                            break;
                        case "video":
                            g = i._tempChange(g, c, h, d, p, f, u + "video.png", t, m, v);
                            e(g).hide().prependTo(a).fadeIn("slow", function () {
                                i._onSelect(t, r)
                            });
                            break;
                        default:
                            if (e.inArray(f, n.newExt) == -1) {
                                u += "file.png"
                            } else {
                                u += f + ".png"
                            }
                            g = i._tempChange(g, c, h, d, p, f, u, t, m, v);
                            e(g).hide().prependTo(a).fadeIn("slow", function () {
                                i._onSelect(t, r)
                            });
                            break
                        }
                    },
                    _tempChange: function (e, t, n, r, i, s, o, u, a, f) {
                        return e.replace(new RegExp("%title%", "g"), t).replace(new RegExp("%original-name%", "g"), n).replace(new RegExp("%type%", "g"), r).replace(new RegExp("%size%", "g"), i).replace(new RegExp("%extension%", "g"), s).replace(new RegExp("%image-url%", "g"), o).replace(new RegExp("%index%", "g"), u).replace(new RegExp("%remove-icon%", "g"), a).replace(new RegExp("%remove-index%", "g"), f)
                    },
                    onChange: function (r, o) {
                        var o = !o ? s.get(0).files : o;
                        i.files = o;
                        if (o) {
                            if (!i._inputFilesCheck()) {
                                i._clearInput();
                                return false
                            } else {
                                i._clearInput(true)
                            }
                            i._setFeedbackText();
                            if (n.showThumbs) {
                                if (n.beforeShow != null && typeof n.beforeShow == "function" ? !n.beforeShow(e(this), a) : false) {
                                    return false
                                }
                                if (n.appendTo != null && n.appendTo != "#filer" && n.appendTo.length != 0) {
                                    e(n.appendTo).html('<div id="input-thumbs"><ul /></div>');
                                    e(n.appendTo).on("click", "#input-thumbs ul li[data-idx] span", function (t) {
                                        i._remove(t, e(this))
                                    });
                                    appendTo = e(n.appendTo);
                                    a = appendTo
                                } else {
                                    a.append('<div id="input-thumbs"><ul /></div>');
                                    appendTo = a
                                }
                                i.appendTo = appendTo;
                                a.on("click", "#input-thumbs ul li[data-idx] span, #input-thumbs ul li[data-idx] div.done-erase", function (t) {
                                    i._remove(t, e(this))
                                });
                                for (t = 0; t < o.length; t++) {
                                    i._thumbBox(t, e(this))
                                }
                            } else {
                                for (t = 0; t < o.length; t++) {
                                    i._onSelect(t, e(this))
                                }
                            }
                        }
                    },
                    _uploadFile: function (t, r, s) {
                        var o = new FormData,
                            u = s[t],
                            f = n.uploadFile,
                            l = a.find("#input-thumbs ul li[data-idx^='" + t + "']");
                        o.append(r, u);
                        f.data && !e.isEmptyObject(f.data) ? e.each(f.data, function (e, t) {
                            o.append(e, t)
                        }) : null;
                        e.ajax({
                            url: f.url,
                            type: "POST",
                            enctype: "multipart/form-data",
                            xhr: function () {
                                myXhr = e.ajaxSettings.xhr();
                                if (myXhr.upload) {
                                    myXhr.upload.addEventListener("progress", function (e) {
                                        i._progressHandling(e, l)
                                    }, false)
                                }
                                return myXhr
                            },
                            beforeSend: function () {
                                l.find(".progress-bar").remove();
                                f.beforeSend != null ? f.beforeSend(l) : null
                            },
                            success: function (e) {
                                f.success != null ? f.success(e, l, l.find(".progress-bar")) : null;
                                t == s.length - 1 && f.onUploaded != null ? f.onUploaded(l) : null
                            },
                            error: function (e) {
                                f.error != null ? f.error(e, l, l.find(".progress-bar")) : null
                            },
                            data: o,
                            cache: false,
                            contentType: false,
                            processData: false
                        })
                    },
                    _progressHandling: function (e, t) {
                        if (e.lengthComputable) {
                            var n = Math.round(e.loaded * 100 / e.total).toString();
                            t.find(".progress-bar").width(n + "%");
                            if (n == 100) {
                                i._progressHide(t)
                            }
                        }
                    },
                    _progressHide: function (e) {
                        setTimeout(function () {
                            n.uploadFile.progressEnd && n.uploadFile.progressEnd != null ? n.uploadFile.progressEnd(e.find(".progress-bar")) : null
                        }, 300)
                    },
                    _onSelect: function (t, r) {
                        if (n.uploadFile && !e.isEmptyObject(n.uploadFile)) {
                            i._uploadFile(t, s.attr("name"), i.files)
                        }
                        if (t + 1 == i.files.length) {
                            n.onSelect != null && typeof n.onSelect == "function" ? n.onSelect(r, a, i.appendTo ? i.appendTo : "undefined") : null
                        }
                    },
                    _dragEnter: function (e) {
                        e.preventDefault();
                        e.stopPropagation()
                    },
                    _dragOver: function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        n.dragDrop.dragOver ? n.dragDrop.dragOver(e, i.dropZone) : null
                    },
                    _dragLeave: function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        n.dragDrop.dragOut ? n.dragDrop.dragOut(e, i.dropZone) : null
                    },
                    _drop: function (e) {
                        e.preventDefault();
                        var t = e.originalEvent.dataTransfer.files,
                            r = new FormData;
                        fileName = s.attr("name");
                        for (var o = 0; o < t.length; o++) {
                            r.append(fileName, t[o])
                        }
                        n.dragDrop.drop ? n.dragDrop.drop(e, r, a) : null;
                        i.onChange(e, t)
                    },
                    _remove: function (t, r) {
                        if (!n.removeFiles || n.removeFiles == false) {
                            return
                        }
                        var s = r.parent().attr("data-idx"),
                            o = a.find("#input-thumbs ul li[data-idx^='" + s + "']"),
                            f = u.length == 0 ? "-0" : u;
                        if (n.onRemove != null && typeof n.onRemove == "function" ? n.onRemove(o, a) : true) {
                            o.fadeOut(300, function () {
                                e(this).remove();
                                $items = a.find("#input-thumbs ul li[data-idx]");
                                if ($items.length > 0) {
                                    var t = a.find('input[name^="inputOrdBox' + f + '"]');
                                    if (t.size() == 0) {
                                        var n = s + ",";
                                        a.append('<input type="hidden" name="inputOrdBox' + f + '" value="' + n + '" />')
                                    } else {
                                        var n = t.val() + s + ",";
                                        a.find('input[name^="inputOrdBox' + f + '"]').val(n)
                                    }
                                    i._setFeedbackText($items.length)
                                } else {
                                    i._clearInput()
                                }
                            })
                        }
                        return false
                    }
                };
            f.init()
        });
        return this
    };
    e.fn.filer.defaults = {
        types: "Image, Audio, Video",
        limit: 12,
        maxSize: 10,
        extensions: ["jpg", "jpeg", "png", "gif", "mp3", "mp4", "wmv"],
        newExt: null,
        changeInput: true,
        showThumbs: true,
        iconPath: "./images/",
        appendTo: null,
        maxChar: 15,
        removeFiles: true,
        template: '<img src="%image-url%" title="%original-name%" /><em>%title%</em> %remove-icon%',
        uploadFile: null,
        dragDrop: null,
        beforeShow: null,
        onSelect: null,
        onRemove: null,
        onEmpty: null,
        inputText: {
            choose: "Choose",
            feedback: "Choose files",
            feedback2: "files were chosen",
            feedback3: "No file chosen"
        }
    }
})(jQuery);
