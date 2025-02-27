import { IEventSystem } from "../../ts-common/events";
import { DataEvents, DiagramEvents, IButtonConfig, IItemConfig, SelectionEvents } from "./types";
import { View } from "../../ts-common/view";
export declare class Toolbar extends View {
    config: any;
    private events;
    private _pressCoords;
    private _handlers;
    private _hidden;
    constructor(events: IEventSystem<DataEvents | DiagramEvents | SelectionEvents>, icons: IButtonConfig[]);
    render(item: any, size: any): any;
    hide(): void;
    show(): void;
    protected _getIcons(item: IItemConfig, icons: IButtonConfig[]): any[];
    protected _getCoords(target: IItemConfig, width: number, height: number): {
        x: number;
        y: number;
    };
}
