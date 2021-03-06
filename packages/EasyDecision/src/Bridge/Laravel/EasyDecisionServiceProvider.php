<?php

declare(strict_types=1);

namespace EonX\EasyDecision\Bridge\Laravel;

use EonX\EasyDecision\Bridge\Common\DecisionFactory as BridgeDecisionFactory;
use EonX\EasyDecision\Bridge\Common\ExpressionLanguageConfigFactory;
use EonX\EasyDecision\Bridge\Common\Interfaces\DecisionFactoryInterface;
use EonX\EasyDecision\Bridge\Common\Interfaces\ExpressionLanguageConfigFactoryInterface;
use EonX\EasyDecision\Bridge\Interfaces\TagsInterface;
use EonX\EasyDecision\Configurators\SetExpressionLanguageConfigurator;
use EonX\EasyDecision\Decisions\DecisionFactory as BaseDecisionFactory;
use EonX\EasyDecision\Expressions\ExpressionFunctionFactory;
use EonX\EasyDecision\Expressions\ExpressionLanguageFactory;
use EonX\EasyDecision\Expressions\Interfaces\ExpressionFunctionFactoryInterface;
use EonX\EasyDecision\Expressions\Interfaces\ExpressionLanguageFactoryInterface;
use EonX\EasyDecision\Interfaces\DecisionFactoryInterface as BaseDecisionFactoryInterface;
use EonX\EasyDecision\Interfaces\ExpressionLanguageRuleFactoryInterface;
use EonX\EasyDecision\Rules\ExpressionLanguageRuleFactory;
use Illuminate\Support\ServiceProvider;

final class EasyDecisionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/config/easy-decision.php' => \base_path('config/easy-decision.php'),
        ]);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/easy-decision.php', 'easy-decision');

        if (\config('easy-decision.use_expression_language', false)) {
            $this->app->bind(SetExpressionLanguageConfigurator::class, function (): SetExpressionLanguageConfigurator {
                return new SetExpressionLanguageConfigurator(
                    $this->app->make(ExpressionLanguageFactoryInterface::class)->create()
                );
            });

            $this->app->tag(SetExpressionLanguageConfigurator::class, [TagsInterface::DECISION_CONFIGURATOR]);
        }

        $this->app->singleton(BaseDecisionFactoryInterface::class, function (): BaseDecisionFactoryInterface {
            return new BaseDecisionFactory(null, $this->app->tagged(TagsInterface::DECISION_CONFIGURATOR));
        });

        $this->app->singleton(ExpressionLanguageRuleFactoryInterface::class, ExpressionLanguageRuleFactory::class);

        $this->registerDeprecatedServices();
    }

    private function registerDeprecatedServices(): void
    {
        $this->app->singleton(ExpressionFunctionFactoryInterface::class, ExpressionFunctionFactory::class);

        $this->app->singleton(
            ExpressionLanguageConfigFactoryInterface::class,
            function (): ExpressionLanguageConfigFactoryInterface {
                return new ExpressionLanguageConfigFactory(\config('easy-decision', []), $this->app);
            }
        );

        $this->app->singleton(
            ExpressionLanguageFactoryInterface::class,
            function (): ExpressionLanguageFactoryInterface {
                return new ExpressionLanguageFactory($this->app->make(ExpressionFunctionFactoryInterface::class));
            }
        );

        $this->app->singleton(DecisionFactoryInterface::class, function (): DecisionFactoryInterface {
            @\trigger_error(\sprintf(
                'Using %s is deprecated since 2.3.7 and will be removed in 3.0, use %s instead',
                DecisionFactoryInterface::class,
                BaseDecisionFactoryInterface::class
            ), \E_USER_DEPRECATED);

            $baseFactory = new BaseDecisionFactory(
                $this->app->make(ExpressionLanguageFactoryInterface::class)
            );

            return new BridgeDecisionFactory(\config('easy-decision', []), $this->app, $baseFactory);
        });
    }
}
