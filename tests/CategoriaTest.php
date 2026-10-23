<?php

declare(strict_types=1);

namespace Sifer\Tests;

use PHPUnit\Framework\TestCase;

final class CategoriaTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
        \reiniciar_base();
    }

    public function testListaCuentaYFiltraCategorias(): void
    {
        self::assertCount(2, \categoria_listar());
        self::assertSame(2, \categoria_contar());

        $filtradas = \categoria_listar('pint');
        self::assertCount(1, $filtradas);
        self::assertSame('Pinturas', $filtradas[0]['nombre']);
        self::assertSame(1, (int) $filtradas[0]['productos']);
    }

    public function testRechazaNombreRepetidoIgnorandoCapitalizacion(): void
    {
        [, $errores] = \categoria_validar(['nombre' => 'hERRAMIENTAS'], null);

        self::assertArrayHasKey('nombre', $errores);
    }

    public function testExceptoIdPermiteConservarElNombrePropio(): void
    {
        [, $errores] = \categoria_validar(['nombre' => 'HERRAMIENTAS'], 1);

        self::assertArrayNotHasKey('nombre', $errores);
    }

    public function testCrearActualizarEliminarYDesvincularProductos(): void
    {
        $id = \categoria_crear('Electricidad', 'Material eléctrico');
        self::assertGreaterThan(0, $id);
        self::assertSame('Electricidad', \categoria_obtener($id)['nombre']);

        self::assertTrue(\categoria_actualizar($id, 'Electricidad y luz', 'Lámparas y cables'));
        $actualizada = \categoria_obtener($id);
        self::assertSame('Electricidad y luz', $actualizada['nombre']);
        self::assertSame('Lámparas y cables', $actualizada['descripcion']);

        self::assertTrue(\categoria_eliminar(1));
        $categoriaProducto = \db()->query('SELECT categoria_id FROM productos WHERE id = 1')->fetchColumn();
        self::assertNull($categoriaProducto);
    }
}
