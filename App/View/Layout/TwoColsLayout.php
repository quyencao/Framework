<?php
/**
 * Created by PhpStorm.
 * User: LaptopTCC
 * Date: 7/26/2017
 * Time: 10:36 AM
 */

namespace App\View\Layout;


use Lib\Layout;

class TwoColsLayout extends Layout
{

    protected function renderLayout($content)
    {
        ?>
            <html>

                <head>
                    <link rel="stylesheet" href="<?php echo $this->themeUrl(); ?>/css/bootstrap.min.css">
                    <link rel="stylesheet" href="<?php echo $this->themeUrl() ?>/css/style.css">
                </head>

                <body>
                    <header>
                        <button class="btn btn-primary">Button</button>
                    </header>
                    <content>
                        <div class="left">
                            Left
                        </div>
                        <div class="mid">
                            <?php echo $content; ?>
                        </div>
                    </content>
                </body>
                <footer>
                    <h1>Footer</h1>
                </footer>
            </html>
        <?php
    }

    public function themeUrl()
    {
        return url('/themes/my-theme');
    }
}