import { IItemConfig } from "../../ts-diagram";
import { BaseDiagramEditor } from "./DiagramEditor";
import { IFreeEditorConfig } from "./types";
export declare class FreeEditor extends BaseDiagramEditor {
    config: IFreeEditorConfig;
    private _changeMode;
    private _shapesBar;
    private _copiedShapes;
    parse(data: any[]): void;
    protected _initDiagram(): void;
    protected _initUI(container: any): void;
    protected _showConnectPoints(id: string, toggle?: boolean): void;
    protected _setHandlers(): void;
    protected _copyShape(): void;
    protected _pasteShape(): void;
    protected _findNearestConnector(e: MouseEvent): void;
    protected _initHotkeys(): void;
    protected _addShape(shape: IItemConfig, x?: number, y?: number): void;
}
