<?php declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\Classes\DuplicateClassNameSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting\SpaceAfterCastSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\DeprecatedFunctionsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\ForbiddenFunctionsSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\PHP\LowerCaseConstantSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Strings\UnnecessaryStringConcatSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\WhiteSpace\DisallowSpaceIndentSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Classes\LowercaseClassKeywordsSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Commenting\DocCommentAlignmentSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Functions\LowercaseFunctionKeywordsSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings\ConcatenationSpacingSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings\DoubleQuoteUsageSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings\EchoedStringsSniff;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use SlevomatCodingStandard\Sniffs\Arrays\TrailingArrayCommaSniff;
use SlevomatCodingStandard\Sniffs\Attributes\DisallowAttributesJoiningSniff;
use SlevomatCodingStandard\Sniffs\Attributes\RequireAttributeAfterDocCommentSniff;
use SlevomatCodingStandard\Sniffs\Classes\BackedEnumTypeSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\ClassConstantVisibilitySniff;
use SlevomatCodingStandard\Sniffs\Classes\ClassMemberSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\ClassStructureSniff;
use SlevomatCodingStandard\Sniffs\Classes\EmptyLinesAroundClassBracesSniff;
use SlevomatCodingStandard\Sniffs\Classes\MethodSpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\ModernClassNameReferenceSniff;
use SlevomatCodingStandard\Sniffs\Classes\PropertyDeclarationSniff;
use SlevomatCodingStandard\Sniffs\Classes\PropertySpacingSniff;
use SlevomatCodingStandard\Sniffs\Classes\RequireMultiLineMethodSignatureSniff;
use SlevomatCodingStandard\Sniffs\Commenting\DocCommentSpacingSniff;
use SlevomatCodingStandard\Sniffs\Commenting\EmptyCommentSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\BlockControlStructureSpacingSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\JumpStatementsSpacingSniff;
use SlevomatCodingStandard\Sniffs\ControlStructures\NewWithParenthesesSniff;
use SlevomatCodingStandard\Sniffs\Exceptions\DeadCatchSniff;
use SlevomatCodingStandard\Sniffs\Exceptions\ReferenceThrowableOnlySniff;
use SlevomatCodingStandard\Sniffs\Functions\ArrowFunctionDeclarationSniff;
use SlevomatCodingStandard\Sniffs\Functions\RequireArrowFunctionSniff;
use SlevomatCodingStandard\Sniffs\Functions\RequireTrailingCommaInCallSniff;
use SlevomatCodingStandard\Sniffs\Functions\RequireTrailingCommaInDeclarationSniff;
use SlevomatCodingStandard\Sniffs\Functions\StrictCallSniff;
use SlevomatCodingStandard\Sniffs\Functions\UnusedInheritedVariablePassedToClosureSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\AlphabeticallySortedUsesSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\DisallowGroupUseSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\FullyQualifiedGlobalConstantsSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\FullyQualifiedGlobalFunctionsSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UseDoesNotStartWithBackslashSniff;
use SlevomatCodingStandard\Sniffs\Namespaces\UseFromSameNamespaceSniff;
use SlevomatCodingStandard\Sniffs\Numbers\RequireNumericLiteralSeparatorSniff;
use SlevomatCodingStandard\Sniffs\Operators\SpreadOperatorSpacingSniff;
use SlevomatCodingStandard\Sniffs\PHP\ReferenceSpacingSniff;
use SlevomatCodingStandard\Sniffs\PHP\ShortListSniff;
use SlevomatCodingStandard\Sniffs\PHP\TypeCastSniff;
use SlevomatCodingStandard\Sniffs\PHP\UselessSemicolonSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DeclareStrictTypesSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DisallowArrayTypeHintSyntaxSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DNFTypeHintFormatSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSpacingSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSpacingSniff;
use SlevomatCodingStandard\Sniffs\Variables\DuplicateAssignmentToVariableSniff;
use SlevomatCodingStandard\Sniffs\Variables\UnusedVariableSniff;
use SlevomatCodingStandard\Sniffs\Variables\UselessVariableSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
	->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
	->withSkip([__DIR__ . '/tests/temp/*'])
	->withCache(directory: __DIR__ . '/tests/temp/ecs')
	->withRules([
		AlphabeticallySortedUsesSniff::class,
		ArrowFunctionDeclarationSniff::class,
		BackedEnumTypeSpacingSniff::class,
		ClassConstantVisibilitySniff::class,
		ClassMemberSpacingSniff::class,
		DeadCatchSniff::class,
		DeprecatedFunctionsSniff::class,
		DisallowAttributesJoiningSniff::class,
		DisallowGroupUseSniff::class,
		DisallowSpaceIndentSniff::class,
		DocCommentAlignmentSniff::class,
		DocCommentSpacingSniff::class,
		DoubleQuoteUsageSniff::class,
		DuplicateAssignmentToVariableSniff::class,
		DuplicateClassNameSniff::class,
		EchoedStringsSniff::class,
		EmptyCommentSniff::class,
		FullyQualifiedGlobalConstantsSniff::class,
		FullyQualifiedGlobalFunctionsSniff::class,
		LowerCaseConstantSniff::class,
		LowercaseClassKeywordsSniff::class,
		LowercaseFunctionKeywordsSniff::class,
		MethodSpacingSniff::class,
		ModernClassNameReferenceSniff::class,
		NewWithParenthesesSniff::class,
		NoUnusedImportsFixer::class,
		ParameterTypeHintSpacingSniff::class,
		PropertyDeclarationSniff::class,
		ReferenceSpacingSniff::class,
		ReferenceThrowableOnlySniff::class,
		RequireArrowFunctionSniff::class,
		RequireAttributeAfterDocCommentSniff::class,
		RequireNumericLiteralSeparatorSniff::class,
		RequireTrailingCommaInCallSniff::class,
		RequireTrailingCommaInDeclarationSniff::class,
		ReturnTypeHintSpacingSniff::class,
		ShortListSniff::class,
		SpreadOperatorSpacingSniff::class,
		StrictCallSniff::class,
		TrailingArrayCommaSniff::class,
		TypeCastSniff::class,
		UnnecessaryStringConcatSniff::class,
		UnusedInheritedVariablePassedToClosureSniff::class,
		UnusedVariableSniff::class,
		UpperCaseConstantNameSniff::class,
		UseDoesNotStartWithBackslashSniff::class,
		UseFromSameNamespaceSniff::class,
		UselessSemicolonSniff::class,
		UselessVariableSniff::class,
	])
	->withConfiguredRule(BlockControlStructureSpacingSniff::class, [
		'controlStructures' => ['if', 'do', 'while', 'for', 'foreach', 'switch', 'try'],
	])
	->withConfiguredRule(ClassStructureSniff::class, [
		'groups' => [
			'uses',
			'enum cases',
			'public constants',
			'protected constants',
			'private constants',
			'public static properties',
			'protected static properties',
			'private static properties',
			'public properties',
			'protected properties',
			'private properties',
			'constructor',
			'methods',
		],
	])
	->withConfiguredRule(ConcatenationSpacingSniff::class, [
		'spacing' => 1,
		'ignoreNewlines' => true,
	])
	->withConfiguredRule(DeclareStrictTypesSniff::class, [
		'declareOnFirstLine' => true,
		'spacesCountAroundEqualsSign' => 0,
	])
	->withConfiguredRule(DisallowArrayTypeHintSyntaxSniff::class, [
		'traversableTypeHints' => [
			'\Traversable',
			'\Generator',
			'\Iterator',
		],
	])
	->withConfiguredRule(DNFTypeHintFormatSniff::class, [
		'shortNullable' => 'no',
		'nullPosition' => 'last',
	])
	->withConfiguredRule(EmptyLinesAroundClassBracesSniff::class, [
		'linesCountAfterOpeningBrace' => 0,
		'linesCountBeforeClosingBrace' => 0,
	])
	->withConfiguredRule(ForbiddenFunctionsSniff::class, [
		'forbiddenFunctions' => [
			'bdump' => null,
			'dump' => null,
			'var_dump' => null,
		],
	])
	->withConfiguredRule(JumpStatementsSpacingSniff::class, [
		'jumpStatements' => ['continue', 'break', 'goto', 'return', 'throw'],
	])
	->withConfiguredRule(PropertySpacingSniff::class, [
		'minLinesCountBeforeWithComment' => 0,
	])
	->withConfiguredRule(RequireMultiLineMethodSignatureSniff::class, [
		'minParametersCount' => 3,
	])
	->withConfiguredRule(SpaceAfterCastSniff::class, [
		'spacing' => 0,
	]);
