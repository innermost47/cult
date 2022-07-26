let tiny = document.getElementsByTagName("textarea");

if (tiny.length > 0) {
  tinymce.init({
    selector: ".tiny",
    plugins: "link",
    content_style:
      "@import url('https://fonts.googleapis.com/css2?family=Oleo+Script&family=Roboto:wght@300&display=swap'); body { font-family: 'Roboto', sans-serif; } h1,h2,h3,h4,h5,h6 { font-family: 'Oleo Script', sans-serif; }",
  });
}
