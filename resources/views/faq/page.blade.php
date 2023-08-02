@extends('theme.faq_page')
@section('content')
    <article class="docs-article" id="section-1">
        <header class="docs-header">
            <h1 class="docs-heading">Introduction <span class="docs-time">Last updated: 2019-06-01</span></h1>
            <section class="docs-intro">
                <p>Section intro goes here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque finibus condimentum nisl id vulputate. Praesent aliquet varius eros interdum suscipit. Donec eu purus sed nibh convallis bibendum quis vitae turpis. Duis vestibulum diam lorem, vitae dapibus nibh facilisis a. Fusce in malesuada odio.</p>
            </section><!--//docs-intro-->

            <h5>Github Code Example:</h5>
            <p>You can <a class="theme-link" href="https://gist.github.com/"  target="_blank">embed your code snippets using Github gists</a></p>
            <div class="docs-code-block">
                <!-- ** Embed github code starts ** -->

                <!-- ** Embed github code ends ** -->
            </div><!--//docs-code-block-->

            <h5>Highlight.js Example:</h5>
            <img src="https://images.sihirbox.com/tutorial/app_login.jpg" alt="Beatles" style="width:auto;">
            <p>You can <a class="theme-link" href="https://github.com/highlightjs/highlight.js" target="_blank">embed your code snippets using highlight.js</a> It supports <a class="theme-link" href="https://highlightjs.org/static/demo/" target="_blank">185 languages and 89 styles</a>.</p>
            <p>This template uses <a class="theme-link" href="https://highlightjs.org/static/demo/" target="_blank">Atom One Dark</a> style for the code blocks: <br><code>&#x3C;link rel=&#x22;stylesheet&#x22; href=&#x22;//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.2/styles/atom-one-dark.min.css&#x22;&#x3E;</code></p>
            <!--//docs-code-block-->
            <iframe width="560" height="315" src="https://www.youtube.com/embed/ulUMTDLQvfo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>

        </header>
    </article>
@endsection