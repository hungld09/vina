<h3>Đề bài</h3>
<div class="web_body">
    <div class="row">
        <div class="detail-question-bank">
            <p><?php echo $result['question']?></p>
        </div>
    </div>
</div>
<h3>Lời giải</h3>
<div class="web_body">
    <div class="row">
        <div class="detail-question-bank">
            <p><?php echo $result['answer']?></p>
        </div>
    </div>
</div>
<style>
    h3{
        margin: 0;
        padding: 0;
        padding-top: 10px;
        font-size: 18px;
        color: #979797;
    }
</style>
<script
    src="//cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML" type="text/javascript">
</script>
<script>
    MathJax.Hub.Config({
        tex2jax: {
            inlineMath: [
                    ['$', '$'],
                    ['\\(', '\\)']
            ]
        }
    });
</script>
