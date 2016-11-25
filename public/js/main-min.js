function setupPositions() {
    left_shop = "20%", left_sport = "40%", left_show = "30"
}
var $shop_car, $shop_pop, $shop_pin, title_line = Tweene.line(),
    t = Tweene.line().delay("2s").loops(-1),
    car_line1 = Tweene.line(),
    car_line2 = Tweene.line(),
    left_shop, left_show, left_sport;
Tweene.defaultTimeUnit = "ms", Tweene.defaultDriver = "velocity", enquire.register("screen and (max-width: 400px)", {
    setup: function() {
        left_shop = "20%", left_sport = "40%", left_show = "30%", console.log("setup")
    },
    match: function() {
        left_shop = "10%", left_sport = "10%", left_show = "10%", console.log("sotto")
    },
    unmatch: function() {
        left_shop = "20%", left_sport = "40%", left_show = "30%", console.log("sopra")
    }
}), jQuery(function($) {
    $shop_car = $("#shop_car"), $sport_car = $("#sport_car"), $show_car = $("#show_car"), $shop_pop = $("#shop_pop"), $sport_pop = $("#sport_pop"), $show_pop = $("#show_pop"), $shop_pin = $("#shop_pin"), $sport_pin = $("#sport_pin"), $show_pin = $("#show_pin");
    var e = Tweene.get($shop_car).from({
            left: "-200px"
        }).to({
            opacity: 1,
            left: left_shop
        }).duration(1e3).easing("easeOutQuad"),
        o = Tweene.get($sport_car).from({
            left: "-200px"
        }).to({
            opacity: 1,
            left: left_sport
        }).duration(1e3).easing("easeOutQuad"),
        n = Tweene.get($show_car).from({
            left: "-200px"
        }).to({
            opacity: 1,
            left: left_show
        }).duration(1e3).easing("easeOutQuad");
    Tweene.set($(".hero__session h1"), {
        opacity: 0,
        top: 50
    }), Tweene.set($(".hero__session h2"), {
        opacity: 0,
        top: 50
    }), Tweene.set($(".hero__session h5"), {
        opacity: 0,
        top: 50
    }), Tweene.set($("#anim_sky"), {
        opacity: 0
    }), Tweene.set($("#animation_bg"), {
        opacity: 0
    }), car_line1.add(o).exec($sport_pop, "popupIn").exec($sport_pop, "popupOut").exec($sport_car, "carOut"), car_line2.add(n).exec($show_pop, "popupIn").exec($show_pop, "popupOut").exec($show_car, "carOut"), $(window).load(function() {
        title_line.exec($(".hero__session h1"), "slideUp").exec($(".hero__session h2"), "slideUp").exec($(".hero__session h5"), "slideUp").exec($("#animation_bg"), "slideUp").exec($("#anim_sky"), "slideUp").play(), t.add(e).exec($shop_pop, "popupIn").exec($shop_pin, "pinIn").exec($sport_pin, "pinIn").exec($show_pin, "pinIn").exec($shop_pop, "popupOut").exec($shop_car, "carOut").add(car_line1).add(car_line2).exec($shop_pin, "pinOut").exec($sport_pin, "pinOut").exec($show_pin, "pinOut").play()
    })
}), Tweene.registerMacro("slideUp", function() {
    this.to({
        top: "0",
        opacity: 1,
        display: "block"
    }).duration(300)
}), Tweene.registerMacro("fadeIn", function() {
    this.to({
        opacity: 1
    }).duration(400)
}), Tweene.registerMacro("popupIn", function() {
    this.from({
        opacity: 0,
        rotate: 90,
        scale: .1
    }).to({
        opacity: 1,
        rotate: 0,
        scale: 1
    }).duration(500).easing("easeOutBack")
}), Tweene.registerMacro("popupOut", function() {
    this.delay(1e3).from({
        opacity: 1,
        rotate: 0,
        scale: 1
    }).to({
        opacity: 0,
        rotate: 90,
        scale: .1
    }).duration(300).easing("easeInExpo")
}), Tweene.registerMacro("pinIn", function() {
    this.from({
        opacity: 0,
        translateY: 100,
        scale: .1
    }).to({
        opacity: 1,
        translateY: 0,
        scale: 1
    }).duration(500).easing("easeOutBack")
}), Tweene.registerMacro("pinOut", function() {
    this.from({
        opacity: 1,
        translateY: 0,
        scale: 1
    }).to({
        opacity: 0,
        translateY: 100,
        scale: .1
    }).duration(400).easing("easeInExpo")
}), Tweene.registerMacro("carOut", function() {
    this.to({
        left: "101%"
    }).duration(700).easing("easeInExpo")
}), jQuery(function($) {
    $(document).ready(function() {
        document.documentElement.className = "js", "ontouchstart" in document.documentElement ? document.documentElement.className += " touch" : document.documentElement.className += " no-touch", $("#toggle").click(function() {
            $(this).toggleClass("active"), $(".menu_main").toggleClass("open")
        }), $("#js-rotating").Morphext({
            animation: "fadeInUp",
            separator: "|",
            speed: 3e3,
            complete: function() {}
        }), $("#doc_upload").change(function() {
            var e = $("#doc_upload")[0].files[0];
            $("#nome_doc").attr("class", "filled"), $("#nome_doc").html(e.name)
        }), $(".documento").hide(), $(".load_file").append('<div id="select_file" class="bottone"><i class="material-icons">&#xE226;</i>Carica</a>'), $("#select_file, #nome_doc").click(function() {
            $("#doc_upload").trigger("click")
        }), $("#app_tabs").responsiveTabs({
            startCollapsed: "accordion"
        }), $("body").on("click", ".r-tabs-tab ", function() {
            var e = $(this).index();
            $(".app_mobile img").hide(), $(".app_mobile img").eq(e).show()
        }), $(".venobox").venobox()
    }), $(window).resize(function() {}), $(window).load(function() {
        $(".loader").velocity({
            opacity: 0
        }, {
            display: "none"
        })
    }), $(window).scroll(function() {})
});